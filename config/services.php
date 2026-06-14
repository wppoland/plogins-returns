<?php
/**
 * Service wiring. Returns a closure that registers every service in the
 * container. Services are thin and self-contained — this plugin has no external
 * runtime dependencies.
 *
 * @package Returns
 */

declare(strict_types=1);

use Returns\Admin\Settings;
use Returns\Container;
use Returns\Migrator;
use Returns\PostType\ReturnRequest;
use Returns\Service\MyReturns;
use Returns\Service\ReturnRequestForm;

defined('ABSPATH') || exit;

return static function (Container $c): void {
    $c->singleton(Migrator::class, static fn (): Migrator => new Migrator());

    // The private custom post type that stores submitted return requests.
    $c->singleton(ReturnRequest::class, static fn (): ReturnRequest => new ReturnRequest());

    // Storefront: the "Request a return" My Account flow (form + submission).
    $c->singleton(ReturnRequestForm::class, static fn (): ReturnRequestForm => new ReturnRequestForm(
        $c->get(ReturnRequest::class),
    ));

    // Storefront: confirmation notice + the customer's own return-status list.
    $c->singleton(MyReturns::class, static fn (): MyReturns => new MyReturns(
        $c->get(ReturnRequest::class),
    ));

    // Admin (only needed in wp-admin context).
    if (is_admin()) {
        $c->singleton(Settings::class, static fn (): Settings => new Settings());
    }
};
