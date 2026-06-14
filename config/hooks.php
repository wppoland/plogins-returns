<?php
/**
 * Boot order: services listed here are resolved from the container and have
 * their registerHooks() called during Plugin::boot(). Each must implement
 * Returns\Contract\HasHooks.
 *
 * @package Returns
 *
 * @return array<class-string>
 */

declare(strict_types=1);

use Returns\Admin\Settings;
use Returns\PostType\ReturnRequest;
use Returns\Service\MyReturns;
use Returns\Service\ReturnRequestForm;

defined('ABSPATH') || exit;

return is_admin()
    ? [
        ReturnRequest::class,
        ReturnRequestForm::class,
        MyReturns::class,
        Settings::class,
    ]
    : [
        ReturnRequest::class,
        ReturnRequestForm::class,
        MyReturns::class,
    ];
