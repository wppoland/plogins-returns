<?php

declare(strict_types=1);

namespace Returns\Service;

defined('ABSPATH') || exit;

/**
 * Lets a guest (an order placed without an account) reach the request form by
 * proving the order number and its billing email, and nothing else.
 *
 * - lookup() answers with the order or null. It never says which half was
 *   wrong, and orders that belong to a registered customer are never returned,
 *   so the lookup cannot be used to reach or probe an account's orders.
 * - throttled() counts every attempt per IP in a FIXED window before any lookup
 *   happens, so the counter itself reveals nothing about a match.
 * - issue()/resolve() bind the visitor to the one order they proved: a signed,
 *   short-lived token carrying the order id. The id is read from the token,
 *   never from the form, so changing an id in a later request reaches nothing.
 *
 * ponytail: stateless HMAC token, no revocation before expiry; store tokens in
 * transients if single-use links are ever needed.
 *
 * No hooks of its own: it is injected into ReturnRequestForm.
 */
final class GuestAccess
{
    /** Token lifetime in seconds. */
    public const TTL = 1800;

    /** Lookup attempts allowed per IP inside one window. */
    public const LIMIT = 5;

    /** Throttle window in seconds. */
    public const WINDOW = 900;

    private const PREFIX = 'redono_lookup_';

    /**
     * The guest order matching an order number and billing email, or null.
     */
    public function lookup(string $number, string $email): ?\WC_Order
    {
        $number = ltrim(trim($number), '#');
        $email  = strtolower(trim($email));

        if ('' === $number || '' === $email) {
            return null;
        }

        // The same filter WooCommerce's order tracking uses, so sequential
        // order-number plugins map the printed number back to the order id.
        $orderId = absint(apply_filters('woocommerce_shortcode_order_tracking_order_id', $number));
        $order   = $orderId > 0 ? wc_get_order($orderId) : false;

        if (! $order instanceof \WC_Order) {
            return null;
        }

        $billing = strtolower(trim((string) $order->get_billing_email()));

        if ('' === $billing || ! hash_equals($billing, $email)) {
            return null;
        }

        // A registered customer's order is reachable only by logging in.
        if (0 !== (int) $order->get_customer_id()) {
            return null;
        }

        return $order;
    }

    /**
     * Count this attempt for an IP, then say whether it is over the limit.
     */
    public function throttled(string $ip, ?int $now = null): bool
    {
        $now    = $now ?? time();
        $key    = self::PREFIX . hash('sha256', $ip);
        $stored = get_transient($key);
        $stored = is_array($stored) ? $stored : [];
        $start  = (int) ($stored['start'] ?? 0);
        $count  = (int) ($stored['n'] ?? 0);

        // Fixed window: the expiry is what is left of it, so steady attempts
        // cannot keep the counter alive forever.
        if ($start < 1 || $start > $now || ($now - $start) >= self::WINDOW) {
            $start = $now;
            $count = 0;
        }

        ++$count;
        set_transient($key, ['n' => $count, 'start' => $start], max(1, self::WINDOW - ($now - $start)));

        return $count > self::LIMIT;
    }

    public function issue(int $orderId, ?int $now = null): string
    {
        $expires = ($now ?? time()) + self::TTL;

        return $orderId . '.' . $expires . '.' . $this->sign($orderId, $expires);
    }

    /**
     * The order id a token grants, or 0 when it is malformed, tampered or expired.
     */
    public function resolve(string $token, ?int $now = null): int
    {
        $parts = explode('.', $token);

        if (3 !== count($parts) || ! ctype_digit($parts[0]) || ! ctype_digit($parts[1])) {
            return 0;
        }

        $orderId = (int) $parts[0];
        $expires = (int) $parts[1];

        if ($orderId < 1 || $expires < ($now ?? time())) {
            return 0;
        }

        return hash_equals($this->sign($orderId, $expires), $parts[2]) ? $orderId : 0;
    }

    /**
     * Strip anything a token can never contain before it is used.
     */
    public static function clean(string $raw): string
    {
        return substr((string) preg_replace('/[^0-9a-f.]/', '', strtolower($raw)), 0, 128);
    }

    private function sign(int $orderId, int $expires): string
    {
        return hash_hmac('sha256', 'redono-guest|' . $orderId . '|' . $expires, wp_salt('auth'));
    }
}
