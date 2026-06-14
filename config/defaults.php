<?php
/**
 * Default settings, merged under the option key `returns_settings`.
 *
 * The plugin ships enabled so the "Request a return" action appears in My
 * Account as soon as WooCommerce is active. Returns are accepted for orders in
 * the statuses listed in `eligible_statuses` within `window_days` of the order.
 *
 * @package Returns
 *
 * @return array<string, mixed>
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

return [
    // Master switch. When off, no return action or form is shown on the storefront.
    'enabled' => true,

    // Order statuses (without the wc- prefix) eligible for a return request.
    'eligible_statuses' => ['completed', 'processing'],

    // Days after the order date during which a return may be requested. 0 = no limit.
    'window_days' => 30,
];
