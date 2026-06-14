<?php

declare(strict_types=1);

namespace Returns\Service;

use Returns\Contract\HasHooks;
use Returns\PostType\ReturnRequest;
use Returns\Support\Options;

defined('ABSPATH') || exit;

/**
 * The customer-facing "Request a return" flow inside WooCommerce My Account.
 *
 * Adds an action to each eligible order, registers a `request-return` account
 * endpoint, renders the item-picker form, and processes the submission. Every
 * step is ownership-checked: only the logged-in owner of an order may open a
 * return for it (no IDOR). All output is escaped; all input is sanitised and
 * nonce-verified.
 */
final class ReturnRequestForm implements HasHooks
{
    public const ENDPOINT = 'request-return';

    private const NONCE = 'returns_submit_request';

    /** @var array<string, string> Validation errors keyed by field. */
    private array $errors = [];

    public function __construct(private readonly ReturnRequest $requests)
    {
    }

    public function registerHooks(): void
    {
        if (! Options::isEnabled()) {
            return;
        }

        add_action('init', [$this, 'addEndpoint']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        add_filter('woocommerce_get_query_vars', [$this, 'addQueryVar']);
        add_filter('woocommerce_account_menu_items', [$this, 'hideEndpointFromMenu'], 10, 1);
        add_action('woocommerce_account_' . self::ENDPOINT . '_endpoint', [$this, 'renderEndpoint']);

        add_filter('woocommerce_my_account_my_orders_actions', [$this, 'orderActions'], 10, 2);
        add_action('woocommerce_order_details_after_order_table', [$this, 'orderViewButton'], 10, 1);

        add_action('template_redirect', [$this, 'maybeHandleSubmit']);
    }

    public function addEndpoint(): void
    {
        add_rewrite_endpoint(self::ENDPOINT, EP_ROOT | EP_PAGES);
    }

    /**
     * Load the storefront stylesheet on My Account pages only.
     */
    public function enqueueAssets(): void
    {
        if (! function_exists('is_account_page') || ! is_account_page()) {
            return;
        }

        wp_enqueue_style(
            'returns',
            RETURNS_URL . 'assets/css/returns.css',
            [],
            \Returns\VERSION,
        );
    }

    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    public function addQueryVar(array $vars): array
    {
        $vars[self::ENDPOINT] = self::ENDPOINT;

        return $vars;
    }

    /**
     * Keep the endpoint out of the account navigation — it is reached only via
     * the per-order "Request a return" action.
     *
     * @param array<string, string> $items
     * @return array<string, string>
     */
    public function hideEndpointFromMenu(array $items): array
    {
        unset($items[self::ENDPOINT]);

        return $items;
    }

    /**
     * Add a "Request a return" action to eligible orders in the orders list.
     *
     * @param array<string, array{url: string, name: string}> $actions
     * @return array<string, array{url: string, name: string}>
     */
    public function orderActions(array $actions, \WC_Order $order): array
    {
        if (! $this->isEligible($order)) {
            return $actions;
        }

        $actions['returns_request'] = [
            'url'  => $this->endpointUrl((int) $order->get_id()),
            'name' => __('Request a return', 'returns'),
        ];

        return $actions;
    }

    /**
     * Show a "Request a return" button on the single order view page.
     */
    public function orderViewButton(\WC_Order $order): void
    {
        if (! is_account_page() || ! $this->isEligible($order)) {
            return;
        }

        if ($this->requests->existsForOrder((int) $order->get_id())) {
            printf(
                '<p class="returns-existing">%s</p>',
                esc_html__('A return request has already been submitted for this order.', 'returns'),
            );

            return;
        }

        printf(
            '<p class="returns-request-cta"><a class="button" href="%1$s">%2$s</a></p>',
            esc_url($this->endpointUrl((int) $order->get_id())),
            esc_html__('Request a return', 'returns'),
        );
    }

    /**
     * Render the return request form for the order in the endpoint URL.
     */
    public function renderEndpoint(mixed $value = null): void
    {
        unset($value);

        $orderId = $this->endpointOrderId();
        $order   = $orderId > 0 ? wc_get_order($orderId) : null;

        echo '<div class="returns-form-wrap">';

        if (! $order instanceof \WC_Order || ! $this->ownsOrder($order)) {
            $this->renderNotice(__('That order could not be found, or you do not have permission to return it.', 'returns'), 'error');
            echo '</div>';

            return;
        }

        if ($this->requests->existsForOrder($orderId)) {
            $this->renderNotice(__('A return request has already been submitted for this order.', 'returns'), 'info');
            echo '</div>';

            return;
        }

        if (! $this->isEligible($order)) {
            $this->renderNotice(__('This order is not eligible for a return.', 'returns'), 'info');
            echo '</div>';

            return;
        }

        $this->renderForm($order);

        echo '</div>';
    }

    /**
     * Handle the POSTed return request early, before output, so we can redirect.
     */
    public function maybeHandleSubmit(): void
    {
        if (! isset($_POST['returns_submit'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified below.
            return;
        }

        $nonce = isset($_POST['returns_nonce'])
            ? sanitize_text_field(wp_unslash($_POST['returns_nonce']))
            : '';

        if (! wp_verify_nonce($nonce, self::NONCE)) {
            $this->errors['_form'] = __('Your session expired. Please try again.', 'returns');

            return;
        }

        if (! is_user_logged_in()) {
            $this->errors['_form'] = __('Please log in to request a return.', 'returns');

            return;
        }

        $orderId = isset($_POST['returns_order_id']) ? absint(wp_unslash($_POST['returns_order_id'])) : 0;
        $order   = $orderId > 0 ? wc_get_order($orderId) : null;

        if (! $order instanceof \WC_Order || ! $this->ownsOrder($order) || ! $this->isEligible($order)) {
            $this->errors['_form'] = __('That order could not be found, or you do not have permission to return it.', 'returns');

            return;
        }

        if ($this->requests->existsForOrder($orderId)) {
            $this->errors['_form'] = __('A return request has already been submitted for this order.', 'returns');

            return;
        }

        $items  = $this->collectItems($order);
        $reason = isset($_POST['returns_reason']) ? sanitize_text_field(wp_unslash($_POST['returns_reason'])) : '';
        $note   = isset($_POST['returns_note']) ? sanitize_textarea_field(wp_unslash($_POST['returns_note'])) : '';

        if ([] === $items) {
            $this->errors['items'] = __('Please select at least one item to return.', 'returns');
        }

        if ('' === $reason) {
            $this->errors['reason'] = __('Please choose a reason for the return.', 'returns');
        }

        if ([] !== $this->errors) {
            return;
        }

        $postId = $this->requests->create((int) $order->get_id(), get_current_user_id(), $items, $reason, $note);

        if ($postId > 0) {
            $this->notifyMerchant($postId, $order, $items, $reason, $note);
        }

        wp_safe_redirect(add_query_arg('returns_sent', '1', wc_get_account_endpoint_url('orders')));
        exit;
    }

    /**
     * Sanitise and resolve the submitted items against the order's real lines,
     * so a tampered POST can never inject products the order never contained.
     *
     * @return array<int, array{item_id: int, name: string, qty: int}>
     */
    private function collectItems(\WC_Order $order): array
    {
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce is verified in maybeHandleSubmit() before this runs.
        // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- keys/values are cast to int below.
        $rawItems = isset($_POST['returns_items']) && is_array($_POST['returns_items'])
            ? wp_unslash($_POST['returns_items'])
            : [];

        $rawQty = isset($_POST['returns_qty']) && is_array($_POST['returns_qty'])
            ? wp_unslash($_POST['returns_qty'])
            : [];
        // phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        // phpcs:enable WordPress.Security.NonceVerification.Missing

        $selected = array_map('absint', (array) $rawItems);
        $items    = [];

        foreach ($order->get_items() as $itemId => $item) {
            $itemId = (int) $itemId;

            if (! in_array($itemId, $selected, true)) {
                continue;
            }

            if (! $item instanceof \WC_Order_Item_Product) {
                continue;
            }

            $maxQty   = max(1, (int) $item->get_quantity());
            $wantQty  = isset($rawQty[$itemId]) ? absint($rawQty[$itemId]) : $maxQty;
            $wantQty  = min(max(1, $wantQty), $maxQty);

            $items[] = [
                'item_id' => $itemId,
                'name'    => $item->get_name(),
                'qty'     => $wantQty,
            ];
        }

        return $items;
    }

    private function renderForm(\WC_Order $order): void
    {
        if (isset($this->errors['_form'])) {
            $this->renderNotice($this->errors['_form'], 'error');
        }
        ?>
        <h2><?php esc_html_e('Request a return', 'returns'); ?></h2>
        <p class="returns-form__order">
            <?php
            printf(
                /* translators: %s: order number */
                esc_html__('Order #%s', 'returns'),
                esc_html((string) $order->get_order_number()),
            );
            ?>
        </p>

        <form method="post" class="returns-form" novalidate>
            <?php wp_nonce_field(self::NONCE, 'returns_nonce'); ?>
            <input type="hidden" name="returns_order_id" value="<?php echo esc_attr((string) $order->get_id()); ?>" />

            <fieldset class="returns-form__items">
                <legend><?php esc_html_e('Which items would you like to return?', 'returns'); ?></legend>
                <?php if (isset($this->errors['items'])) : ?>
                    <span class="returns-form__error" role="alert"><?php echo esc_html($this->errors['items']); ?></span>
                <?php endif; ?>

                <table class="returns-form__table">
                    <thead>
                        <tr>
                            <th scope="col"><span class="screen-reader-text"><?php esc_html_e('Select', 'returns'); ?></span></th>
                            <th scope="col"><?php esc_html_e('Product', 'returns'); ?></th>
                            <th scope="col"><?php esc_html_e('Quantity', 'returns'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order->get_items() as $itemId => $item) :
                            if (! $item instanceof \WC_Order_Item_Product) {
                                continue;
                            }
                            $itemId = (int) $itemId;
                            $maxQty = max(1, (int) $item->get_quantity());
                            ?>
                            <tr>
                                <td data-label="<?php esc_attr_e('Select', 'returns'); ?>">
                                    <input type="checkbox" id="returns-item-<?php echo esc_attr((string) $itemId); ?>"
                                        name="returns_items[]" value="<?php echo esc_attr((string) $itemId); ?>" />
                                </td>
                                <td data-label="<?php esc_attr_e('Product', 'returns'); ?>">
                                    <label for="returns-item-<?php echo esc_attr((string) $itemId); ?>">
                                        <?php echo esc_html($item->get_name()); ?>
                                    </label>
                                </td>
                                <td data-label="<?php esc_attr_e('Quantity', 'returns'); ?>">
                                    <label class="screen-reader-text" for="returns-qty-<?php echo esc_attr((string) $itemId); ?>">
                                        <?php esc_html_e('Quantity to return', 'returns'); ?>
                                    </label>
                                    <input type="number" min="1" step="1" max="<?php echo esc_attr((string) $maxQty); ?>"
                                        id="returns-qty-<?php echo esc_attr((string) $itemId); ?>"
                                        name="returns_qty[<?php echo esc_attr((string) $itemId); ?>]"
                                        value="<?php echo esc_attr((string) $maxQty); ?>"
                                        class="returns-form__qty" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </fieldset>

            <p class="returns-form__field">
                <label for="returns-reason"><?php esc_html_e('Reason', 'returns'); ?> <span class="returns-form__req" aria-hidden="true">*</span></label>
                <select id="returns-reason" name="returns_reason" required
                    <?php echo isset($this->errors['reason']) ? 'aria-invalid="true" aria-describedby="returns-reason-error"' : ''; ?>>
                    <option value=""><?php esc_html_e('Select a reason…', 'returns'); ?></option>
                    <?php foreach ($this->reasons() as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($this->errors['reason'])) : ?>
                    <span class="returns-form__error" id="returns-reason-error" role="alert"><?php echo esc_html($this->errors['reason']); ?></span>
                <?php endif; ?>
            </p>

            <p class="returns-form__field">
                <label for="returns-note"><?php esc_html_e('Additional details', 'returns'); ?></label>
                <textarea id="returns-note" name="returns_note" rows="4"></textarea>
            </p>

            <p class="returns-form__submit">
                <button type="submit" name="returns_submit" value="1" class="button alt"><?php esc_html_e('Submit return request', 'returns'); ?></button>
                <a class="returns-form__cancel" href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>"><?php esc_html_e('Cancel', 'returns'); ?></a>
            </p>
        </form>
        <?php
    }

    private function renderNotice(string $message, string $type): void
    {
        printf(
            '<div class="returns-notice returns-notice--%1$s" role="%2$s">%3$s</div>',
            esc_attr($type),
            esc_attr('error' === $type ? 'alert' : 'status'),
            esc_html($message),
        );
    }

    /**
     * Built-in return reasons.
     *
     * @return array<string, string>
     */
    private function reasons(): array
    {
        return [
            'damaged'    => __('Arrived damaged or faulty', 'returns'),
            'wrong_item' => __('Wrong item received', 'returns'),
            'not_needed' => __('No longer needed', 'returns'),
            'size_fit'   => __('Size or fit issue', 'returns'),
            'other'      => __('Other', 'returns'),
        ];
    }

    /**
     * Email the merchant about a new return request.
     *
     * @param array<int, array{item_id: int, name: string, qty: int}> $items
     */
    private function notifyMerchant(int $postId, \WC_Order $order, array $items, string $reason, string $note): void
    {
        $recipient = (string) get_option('admin_email');

        $lines   = [];
        $lines[] = sprintf(
            /* translators: 1: order number, 2: site name */
            __('A new return request was submitted for order #%1$s on %2$s.', 'returns'),
            (string) $order->get_order_number(),
            wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES),
        );
        $lines[] = '';
        $lines[] = __('Customer:', 'returns') . ' ' . trim($order->get_formatted_billing_full_name());
        $lines[] = __('Reason:', 'returns') . ' ' . $this->reasonLabel($reason);
        $lines[] = '';
        $lines[] = __('Requested items:', 'returns');

        foreach ($items as $item) {
            $lines[] = sprintf('- %1$s x %2$d', $item['name'], $item['qty']);
        }

        if ('' !== $note) {
            $lines[] = '';
            $lines[] = __('Customer note:', 'returns');
            $lines[] = $note;
        }

        $editLink = get_edit_post_link($postId, 'raw');

        if (is_string($editLink) && '' !== $editLink) {
            $lines[] = '';
            $lines[] = __('Manage in admin:', 'returns') . ' ' . $editLink;
        }

        $subject = sprintf(
            /* translators: %s: order number */
            __('New return request for order #%s', 'returns'),
            (string) $order->get_order_number(),
        );

        wp_mail($recipient, $subject, implode("\n", $lines));
    }

    private function reasonLabel(string $reason): string
    {
        $reasons = $this->reasons();

        return $reasons[$reason] ?? $reason;
    }

    /**
     * Whether an order is within the configured return window and status set.
     */
    private function isEligible(\WC_Order $order): bool
    {
        if (! in_array($order->get_status(), Options::eligibleStatuses(), true)) {
            return false;
        }

        $window = Options::windowDays();

        if ($window > 0) {
            $created = $order->get_date_created();

            if ($created instanceof \WC_DateTime) {
                $ageDays = (int) floor((time() - $created->getTimestamp()) / DAY_IN_SECONDS);

                if ($ageDays > $window) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Ownership check: the current user must own the order (no IDOR).
     */
    private function ownsOrder(\WC_Order $order): bool
    {
        $userId = get_current_user_id();

        return $userId > 0 && (int) $order->get_customer_id() === $userId;
    }

    private function endpointOrderId(): int
    {
        // Read-only navigation parameter; no state change occurs here.
        return isset($_GET['order_id']) ? absint(wp_unslash($_GET['order_id'])) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    }

    private function endpointUrl(int $orderId): string
    {
        return add_query_arg('order_id', $orderId, wc_get_account_endpoint_url(self::ENDPOINT));
    }
}
