# Returns - RMA and Return Requests for WooCommerce

Self-service returns (RMA) for WooCommerce. Customers open a return request from
**My Account → Orders** on an eligible order — picking items, a quantity, a
reason and an optional note. Each request is saved as a private custom post type
linked to the order, emailed to the merchant, and managed in wp-admin through a
status workflow (requested / approved / rejected / completed) the customer can
follow from their account.

This is the FREE, WordPress.org-bound plugin. It is **self-contained** — no
runtime Composer dependencies. A premium add-on, **Returns Pro**, lives in a
separate repository.

## Architecture

- `returns.php` — bootstrap: HPOS declaration, WooCommerce guard, boots on `init:0`.
- `src/Plugin.php` — DI container boot; fires `do_action('returns/booted', $plugin)`.
- `src/PostType/ReturnRequest.php` — the private `returns_rma` CPT, admin columns,
  details + status meta boxes; fires `returns/status_changed` when status changes.
- `src/Service/ReturnRequestForm.php` — the My Account `request-return` endpoint,
  the item-picker form, and the ownership-checked submission handler.
- `src/Service/MyReturns.php` — confirmation notice + the customer's status list.
- `src/Admin/Settings.php` — the WooCommerce → Returns settings page.
- `src/Support/` — `Options` (typed settings accessor) and `Statuses` (status labels).

## Development

```bash
composer install
composer cs        # PHPCS (WordPress security sniffs)
composer analyse   # PHPStan level 6
```

Plugin Check runs in CI via the reusable `wppoland/workflows` pipeline.

## Extensibility

- `returns/booted` — fires after boot with the `Plugin` instance (PRO hooks here).
- `returns/status_changed` — `($postId, $newStatus, $previousStatus)`.
- `returns/reasons` — filter the list of selectable return reasons.
