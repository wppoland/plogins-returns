<?php

declare(strict_types=1);

namespace Returns\Service;

use Returns\Contract\HasHooks;
use Returns\PostType\ReturnRequest;
use Returns\Support\Options;
use Returns\Support\Statuses;

defined('ABSPATH') || exit;

/**
 * Customer-facing surface for submitted returns: a success notice after a
 * request is sent, plus a status list of the customer's own returns shown under
 * the orders endpoint. All data is filtered by the current user (no IDOR).
 */
final class MyReturns implements HasHooks
{
    public function __construct(private readonly ReturnRequest $requests)
    {
    }

    public function registerHooks(): void
    {
        if (! Options::isEnabled()) {
            return;
        }

        add_action('woocommerce_account_orders_endpoint', [$this, 'renderOnOrders'], 5);
    }

    /**
     * Render the confirmation notice and the customer's return-status list above
     * the orders table.
     */
    public function renderOnOrders(): void
    {
        // Read-only confirmation flag.
        $sent = isset($_GET['returns_sent']) && '1' === sanitize_text_field(wp_unslash($_GET['returns_sent'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ($sent) {
            printf(
                '<div class="returns-notice returns-notice--success" role="status"><strong>%1$s</strong> %2$s</div>',
                esc_html__('Return requested.', 'returns'),
                esc_html__('We have received your return request and will be in touch shortly.', 'returns'),
            );
        }

        $this->renderList();
    }

    private function renderList(): void
    {
        $ids = $this->requests->findByCustomer(get_current_user_id());

        if ([] === $ids) {
            return;
        }
        ?>
        <section class="returns-list" aria-labelledby="returns-list-heading">
            <h2 id="returns-list-heading"><?php esc_html_e('Your return requests', 'returns'); ?></h2>
            <table class="returns-list__table shop_table">
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e('Request', 'returns'); ?></th>
                        <th scope="col"><?php esc_html_e('Order', 'returns'); ?></th>
                        <th scope="col"><?php esc_html_e('Date', 'returns'); ?></th>
                        <th scope="col"><?php esc_html_e('Status', 'returns'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ids as $postId) :
                        $orderId = (int) get_post_meta($postId, ReturnRequest::META_ORDER_ID, true);
                        $status  = $this->requests->status($postId);
                        $date    = get_the_date('', $postId);
                        ?>
                        <tr>
                            <td data-title="<?php esc_attr_e('Request', 'returns'); ?>">#<?php echo esc_html((string) $postId); ?></td>
                            <td data-title="<?php esc_attr_e('Order', 'returns'); ?>"><?php echo esc_html($orderId > 0 ? '#' . $orderId : '—'); ?></td>
                            <td data-title="<?php esc_attr_e('Date', 'returns'); ?>"><?php echo esc_html(is_string($date) ? $date : ''); ?></td>
                            <td data-title="<?php esc_attr_e('Status', 'returns'); ?>">
                                <span class="returns-badge returns-badge--<?php echo esc_attr(Statuses::slug($status)); ?>">
                                    <?php echo esc_html(Statuses::label($status)); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php
    }
}
