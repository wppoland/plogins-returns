<?php

/**
 * Every capability of the return request post type must resolve to
 * manage_woocommerce, and return requests must not be creatable from wp-admin.
 *
 * The post type used capability_type 'post' with map_meta_cap, so an Editor
 * could list, open, edit and delete every customer's return request (name,
 * email, order) by direct link. Same fix as Quotlet 1.1.8.
 *
 * Run: php tests/return-capabilities-check.php
 */

declare(strict_types=1);

namespace Returns\Contract {
    interface HasHooks
    {
    }
}

namespace {
    define('ABSPATH', __DIR__);

    $registered = [];

    function post_type_exists(string $type): bool
    {
        return false;
    }

    function __(string $text, string $domain = 'default'): string
    {
        return $text;
    }

    function register_post_type(string $type, array $args): void
    {
        $GLOBALS['registered'] = $args;
    }

    require __DIR__ . '/../src/PostType/ReturnRequest.php';

    (new \Returns\PostType\ReturnRequest())->register();

    $args     = $registered;
    $caps     = $args['capabilities'] ?? [];
    $failures = [];

    // Every key get_post_type_capabilities() can return, so none falls back to a post cap.
    $needed = [
        'edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts',
        'edit_private_posts', 'edit_published_posts', 'publish_posts', 'read_private_posts',
        'read', 'delete_posts', 'delete_private_posts', 'delete_published_posts',
        'delete_others_posts', 'create_posts',
    ];

    if (false !== ($args['map_meta_cap'] ?? null)) {
        $failures[] = 'map_meta_cap must be false';
    }

    if (isset($args['capability_type']) && 'post' === $args['capability_type']) {
        $failures[] = "capability_type 'post' would reuse Editor caps";
    }

    foreach ($needed as $cap) {
        $want = 'create_posts' === $cap ? 'do_not_allow' : 'manage_woocommerce';
        $got  = $caps[$cap] ?? '(unset)';
        if ($got !== $want) {
            $failures[] = "{$cap} is {$got}, expected {$want}";
        }
    }

    foreach ($failures as $failure) {
        echo "FAIL: {$failure}\n";
    }

    echo [] === $failures ? "OK: return request caps all resolve to manage_woocommerce\n" : '';
    exit([] === $failures ? 0 : 1);
}
