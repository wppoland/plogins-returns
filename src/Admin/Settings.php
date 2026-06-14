<?php

declare(strict_types=1);

namespace Returns\Admin;

defined('ABSPATH') || exit;

use Returns\Contract\HasHooks;
use Returns\PostType\ReturnRequest;
use Returns\Support\Options;

/**
 * Admin settings page registered as a WooCommerce submenu.
 *
 * Stores everything in the `returns_settings` option (array): the master
 * toggle, the eligible order statuses and the return window. All output is
 * escaped; all input is sanitised on save. The page also links to the saved
 * Return Requests list.
 */
final class Settings implements HasHooks
{
    private const PAGE  = 'returns-settings';
    private const GROUP = 'returns_settings_group';

    public function registerHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(string $hook): void
    {
        // Load the shared admin stylesheet on our settings page and on the
        // return-request CPT screens (for the status badges).
        $screen   = function_exists('get_current_screen') ? get_current_screen() : null;
        $isCpt    = $screen instanceof \WP_Screen && ReturnRequest::POST_TYPE === $screen->post_type;
        $isOurs   = 'woocommerce_page_' . self::PAGE === $hook;

        if (! $isCpt && ! $isOurs) {
            return;
        }

        wp_enqueue_style(
            'returns-admin',
            RETURNS_URL . 'assets/css/admin.css',
            [],
            \Returns\VERSION,
        );
    }

    public function addMenuPage(): void
    {
        add_submenu_page(
            'woocommerce',
            __('Returns — RMA settings', 'returns'),
            __('Returns', 'returns'),
            'manage_woocommerce',
            self::PAGE,
            [$this, 'renderPage'],
        );
    }

    public function registerSettings(): void
    {
        register_setting(
            self::GROUP,
            Options::OPTION,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
            ],
        );

        add_filter(
            'option_page_capability_' . self::GROUP,
            static fn (): string => 'manage_woocommerce',
        );
    }

    public function renderPage(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            return;
        }

        $settings = Options::all();
        $eligible = Options::eligibleStatuses();
        $listUrl  = admin_url('edit.php?post_type=' . ReturnRequest::POST_TYPE);
        ?>
        <div class="wrap returns-admin">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="returns-intro">
                <h2><?php esc_html_e('Let customers request returns from their account', 'returns'); ?></h2>
                <p>
                    <?php esc_html_e('Customers open a return from My Account → Orders: they pick items, a quantity, a reason and an optional note. Each request is emailed to you and saved as a private record you manage here, with a status the customer can follow.', 'returns'); ?>
                </p>
                <p>
                    <a class="button" href="<?php echo esc_url($listUrl); ?>"><?php esc_html_e('View return requests', 'returns'); ?></a>
                </p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields(self::GROUP); ?>

                <div class="returns-card">
                    <h2><?php esc_html_e('General', 'returns'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><?php esc_html_e('Enable returns', 'returns'); ?></th>
                                <td>
                                    <label for="returns_enabled">
                                        <input type="checkbox" id="returns_enabled"
                                            name="<?php echo esc_attr(Options::OPTION); ?>[enabled]" value="1"
                                            <?php checked((bool) ($settings['enabled'] ?? false), true); ?> />
                                        <?php esc_html_e('Allow customers to request returns from My Account.', 'returns'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('When off, the "Request a return" action and form are hidden everywhere on the storefront.', 'returns'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Eligible order statuses', 'returns'); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text"><?php esc_html_e('Eligible order statuses', 'returns'); ?></legend>
                                        <?php foreach ($this->orderStatuses() as $key => $label) : ?>
                                            <label class="returns-checkbox">
                                                <input type="checkbox"
                                                    name="<?php echo esc_attr(Options::OPTION); ?>[eligible_statuses][]"
                                                    value="<?php echo esc_attr($key); ?>"
                                                    <?php checked(in_array($key, $eligible, true), true); ?> />
                                                <?php echo esc_html($label); ?>
                                            </label><br />
                                        <?php endforeach; ?>
                                    </fieldset>
                                    <p class="description"><?php esc_html_e('Only orders in these statuses can be returned.', 'returns'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="returns_window_days"><?php esc_html_e('Return window (days)', 'returns'); ?></label>
                                </th>
                                <td>
                                    <input type="number" min="0" step="1" id="returns_window_days" class="small-text"
                                        name="<?php echo esc_attr(Options::OPTION); ?>[window_days]"
                                        value="<?php echo esc_attr((string) ($settings['window_days'] ?? 30)); ?>" />
                                    <p class="description"><?php esc_html_e('How many days after the order date a return may be requested. 0 = no time limit.', 'returns'); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * The available WooCommerce order statuses, without the wc- prefix.
     *
     * @return array<string, string>
     */
    private function orderStatuses(): array
    {
        $statuses = function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : [];
        $clean    = [];

        foreach ($statuses as $key => $label) {
            $clean[(string) preg_replace('/^wc-/', '', (string) $key)] = (string) $label;
        }

        if ([] === $clean) {
            $clean = [
                'processing' => __('Processing', 'returns'),
                'completed'  => __('Completed', 'returns'),
            ];
        }

        return $clean;
    }

    /**
     * Sanitise the submitted settings before save.
     *
     * @param mixed $raw
     * @return array<string, mixed>
     */
    public function sanitize(mixed $raw): array
    {
        if (! is_array($raw)) {
            $raw = [];
        }

        $valid    = array_keys($this->orderStatuses());
        $eligible = [];

        if (isset($raw['eligible_statuses']) && is_array($raw['eligible_statuses'])) {
            foreach ($raw['eligible_statuses'] as $status) {
                $status = sanitize_key((string) $status);
                if (in_array($status, $valid, true)) {
                    $eligible[] = $status;
                }
            }
        }

        if ([] === $eligible) {
            $eligible = ['completed'];
        }

        return [
            'enabled'           => ! empty($raw['enabled']),
            'eligible_statuses' => array_values(array_unique($eligible)),
            'window_days'       => isset($raw['window_days']) ? max(0, absint($raw['window_days'])) : 30,
        ];
    }
}
