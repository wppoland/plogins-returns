<?php

declare(strict_types=1);

namespace Returns\Service;

use Returns\Contract\HasHooks;
use Returns\PostType\ReturnRequest;
use Returns\Support\Reasons;
use Returns\Support\Statuses;
use Returns\Support\Types;
use WP_Query;
use WP_User;

defined('ABSPATH') || exit;

/**
 * Personal data exporter and eraser for Return / RMA requests.
 */
final class ReturnPrivacyService implements HasHooks
{
    private const PAGE_SIZE = 100;

    public function registerHooks(): void
    {
        add_filter('wp_privacy_personal_data_exporters', [$this, 'registerExporters']);
        add_filter('wp_privacy_personal_data_erasers', [$this, 'registerErasers']);
    }

    /**
     * @param array<string, array<string, mixed>> $exporters
     * @return array<string, array<string, mixed>>
     */
    public function registerExporters(array $exporters): array
    {
        $exporters['returns-requests'] = [
            'exporter_friendly_name' => __('Return & RMA Requests', 'plogins-returns'),
            'callback'               => [$this, 'exportRequests'],
        ];

        return $exporters;
    }

    /**
     * @param array<string, array<string, mixed>> $erasers
     * @return array<string, array<string, mixed>>
     */
    public function registerErasers(array $erasers): array
    {
        $erasers['returns-requests'] = [
            'eraser_friendly_name' => __('Return & RMA Requests', 'plogins-returns'),
            'callback'             => [$this, 'eraseRequests'],
        ];

        return $erasers;
    }

    /**
     * @return array{data: list<array<string, mixed>>, done: bool}
     */
    public function exportRequests(string $email, int $page = 1): array
    {
        $page   = max(1, $page);
        $postIds = $this->findRmaPostIds($email, $page);

        $items = [];
        foreach ($postIds as $postId) {
            $orderId    = (int) get_post_meta($postId, ReturnRequest::META_ORDER_ID, true);
            $reasonKey  = (string) get_post_meta($postId, ReturnRequest::META_REASON, true);
            $note       = (string) get_post_meta($postId, ReturnRequest::META_NOTE, true);
            $statusKey  = (string) get_post_meta($postId, ReturnRequest::META_STATUS, true);
            $typeKey    = (string) get_post_meta($postId, ReturnRequest::META_TYPE, true);
            $postDate   = (string) get_the_date('Y-m-d H:i:s', $postId);

            $reason = Reasons::label($reasonKey);
            $status = Statuses::label($statusKey);
            $type   = Types::label($typeKey);

            $items[] = [
                'group_id'    => 'returns-requests',
                'group_label' => __('Return & RMA Requests', 'plogins-returns'),
                'item_id'     => 'returns-rma-' . $postId,
                'data'        => [
                    ['name' => __('Request ID', 'plogins-returns'), 'value' => (string) $postId],
                    ['name' => __('Order ID', 'plogins-returns'), 'value' => (string) $orderId],
                    ['name' => __('Type', 'plogins-returns'), 'value' => $type],
                    ['name' => __('Reason', 'plogins-returns'), 'value' => $reason],
                    ['name' => __('Customer Note', 'plogins-returns'), 'value' => $note],
                    ['name' => __('Status', 'plogins-returns'), 'value' => $status],
                    ['name' => __('Date', 'plogins-returns'), 'value' => $postDate],
                ],
            ];
        }

        return [
            'data' => $items,
            'done' => count($postIds) < self::PAGE_SIZE,
        ];
    }

    /**
     * @return array{items_removed: int, items_retained: int, messages: list<string>, done: bool}
     */
    public function eraseRequests(string $email, int $page = 1): array
    {
        $page    = max(1, $page);
        $postIds = $this->findRmaPostIds($email, $page);

        $anonymized = 0;
        foreach ($postIds as $postId) {
            update_post_meta($postId, ReturnRequest::META_CUSTOMER_ID, 0);
            update_post_meta($postId, ReturnRequest::META_NOTE, '');
            $anonymized++;
        }

        return [
            'items_removed'  => $anonymized,
            'items_retained' => $anonymized, // Retained for accounting/statutory warranty requirements
            'messages'       => $anonymized > 0
                ? [__('Customer personal notes and IDs removed from RMA requests; return records retained for statutory bookkeeping.', 'plogins-returns')]
                : [],
            'done'           => count($postIds) < self::PAGE_SIZE,
        ];
    }

    /**
     * @return list<int>
     */
    private function findRmaPostIds(string $email, int $page): array
    {
        $user = get_user_by('email', $email);
        $userId = $user instanceof WP_User ? (int) $user->ID : 0;

        $orderIds = [];
        if (function_exists('wc_get_orders')) {
            $orders = wc_get_orders([
                'billing_email' => $email,
                'limit'         => -1,
                'return'        => 'ids',
            ]);
            if (is_array($orders)) {
                $orderIds = array_map('intval', $orders);
            }
        }

        $metaQueries = [];
        if ($userId > 0) {
            $metaQueries[] = [
                'key'     => ReturnRequest::META_CUSTOMER_ID,
                'value'   => $userId,
                'compare' => '=',
            ];
        }
        if (! empty($orderIds)) {
            $metaQueries[] = [
                'key'     => ReturnRequest::META_ORDER_ID,
                'value'   => $orderIds,
                'compare' => 'IN',
            ];
        }

        if (empty($metaQueries)) {
            return [];
        }

        $queryArgs = [
            'post_type'      => ReturnRequest::POST_TYPE,
            'post_status'    => 'any',
            'posts_per_page' => self::PAGE_SIZE,
            'paged'          => $page,
            'fields'         => 'ids',
            'meta_query'     => array_merge(['relation' => 'OR'], $metaQueries),
        ];

        $query = new WP_Query($queryArgs);
        /** @var list<int> $posts */
        $posts = is_array($query->posts) ? array_map('intval', $query->posts) : [];

        return $posts;
    }
}
