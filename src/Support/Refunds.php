<?php

declare(strict_types=1);

namespace Returns\Support;

use Returns\PostType\ReturnRequest;

defined('ABSPATH') || exit;

/**
 * WooCommerce order refund helpers for return requests.
 *
 * Extensions call create() to issue a refund against the linked order and
 * listen to returns/order_refund for follow-up automation.
 */
final class Refunds
{
    public const META_ORDER_REFUND_ID = '_returns_order_refund_id';

    public static function hasRefund(int $postId): bool
    {
        return self::refundId($postId) > 0;
    }

    public static function refundId(int $postId): int
    {
        return max(0, (int) get_post_meta($postId, self::META_ORDER_REFUND_ID, true));
    }

    public static function orderId(int $postId): int
    {
        return max(0, (int) get_post_meta($postId, ReturnRequest::META_ORDER_ID, true));
    }

    /**
     * @param array{
     *     refund_payment?: bool,
     *     restock_items?: bool
     * } $args
     * @return int|\WP_Error Refund post ID on success.
     */
    public static function create(int $postId, float $amount, string $reason = '', array $args = []): int|\WP_Error
    {
        if ($postId <= 0) {
            return new \WP_Error('returns_refund', __('Invalid return request.', 'plogins-returns'));
        }

        if ($amount <= 0) {
            return new \WP_Error('returns_refund', __('Refund amount must be greater than zero.', 'plogins-returns'));
        }

        if (self::hasRefund($postId)) {
            return new \WP_Error('returns_refund', __('This return request already has a linked order refund.', 'plogins-returns'));
        }

        if (! function_exists('wc_create_refund') || ! function_exists('wc_get_order')) {
            return new \WP_Error('returns_refund', __('WooCommerce is not available.', 'plogins-returns'));
        }

        $orderId = self::orderId($postId);

        if ($orderId <= 0) {
            return new \WP_Error('returns_refund', __('No order is linked to this return request.', 'plogins-returns'));
        }

        $order = wc_get_order($orderId);

        if (! $order instanceof \WC_Order) {
            return new \WP_Error('returns_refund', __('The linked order could not be loaded.', 'plogins-returns'));
        }

        $remaining = (float) $order->get_total() - (float) $order->get_total_refunded();

        if ($remaining <= 0) {
            return new \WP_Error('returns_refund', __('This order has no refundable balance remaining.', 'plogins-returns'));
        }

        $amount = min($amount, $remaining);

        $refundPayment = ! empty($args['refund_payment']);
        $restockItems  = ! empty($args['restock_items']);
        $lineItems     = $restockItems ? self::lineItemsForRefund($order, $postId, $amount) : [];

        $refund = wc_create_refund(
            [
                'amount'         => $amount,
                'reason'         => sanitize_text_field($reason),
                'order_id'       => $orderId,
                'line_items'     => $lineItems,
                'refund_payment' => $refundPayment,
                'restock_items'  => $restockItems,
            ],
        );

        if (is_wp_error($refund)) {
            return $refund;
        }

        if (! $refund instanceof \WC_Order_Refund) {
            return new \WP_Error('returns_refund', __('The refund could not be created.', 'plogins-returns'));
        }

        $refundId = (int) $refund->get_id();

        if ($refundId <= 0) {
            return new \WP_Error('returns_refund', __('The refund could not be created.', 'plogins-returns'));
        }

        update_post_meta($postId, self::META_ORDER_REFUND_ID, $refundId);

        /**
         * Fires after a WooCommerce refund is created for a return request.
         *
         * @param int   $postId   The return request post ID.
         * @param int   $orderId  The WooCommerce order ID.
         * @param int   $refundId The WooCommerce refund post ID.
         * @param float $amount   The refunded amount.
         */
        do_action('returns/order_refund', $postId, $orderId, $refundId, $amount);

        return $refundId;
    }

    /**
     * @return array<int, array{qty: int, refund_total: float, refund_tax: array<int|string, float>}>
     */
    private static function lineItemsForRefund(\WC_Order $order, int $postId, float $amount): array
    {
        $rawItems = get_post_meta($postId, ReturnRequest::META_ITEMS, true);

        if (! is_array($rawItems) || [] === $rawItems) {
            return [];
        }

        $lineItems = [];
        $allocated = 0.0;

        foreach ($rawItems as $item) {
            if (! is_array($item)) {
                continue;
            }

            $orderItemId = isset($item['item_id']) ? (int) $item['item_id'] : 0;
            $qty         = isset($item['qty']) ? max(1, (int) $item['qty']) : 1;

            if ($orderItemId <= 0) {
                continue;
            }

            $orderItem = $order->get_item($orderItemId);

            if (! $orderItem instanceof \WC_Order_Item_Product) {
                continue;
            }

            $itemQty   = max(1, (int) $orderItem->get_quantity());
            $ratio     = min(1.0, $qty / $itemQty);
            $lineTotal = round((float) $orderItem->get_total() * $ratio, wc_get_price_decimals());
            $taxes     = $orderItem->get_taxes();
            $refundTax = [];

            if (is_array($taxes) && isset($taxes['total']) && is_array($taxes['total'])) {
                foreach ($taxes['total'] as $taxId => $taxAmount) {
                    $refundTax[$taxId] = round((float) $taxAmount * $ratio, wc_get_price_decimals());
                }
            }

            $lineItems[$orderItemId] = [
                'qty'          => $qty,
                'refund_total' => $lineTotal,
                'refund_tax'   => $refundTax,
            ];

            $allocated += $lineTotal + array_sum($refundTax);
        }

        if ([] === $lineItems || $allocated <= 0) {
            return [];
        }

        if (abs($allocated - $amount) > 0.01) {
            $keys    = array_keys($lineItems);
            $lastKey = (int) end($keys);
            $delta   = round($amount - $allocated, wc_get_price_decimals());
            $lineItems[$lastKey]['refund_total'] = max(
                0.0,
                round((float) $lineItems[$lastKey]['refund_total'] + $delta, wc_get_price_decimals()),
            );
        }

        return $lineItems;
    }
}
