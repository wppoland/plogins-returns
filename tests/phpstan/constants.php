<?php
/**
 * Constants needed by PHPStan to analyse the plugin without bootstrapping
 * WordPress or running the main plugin file.
 *
 * @package Returns
 */

declare(strict_types=1);

namespace {
    if (! defined('ABSPATH')) {
        define('ABSPATH', '/tmp/wordpress/');
    }
    if (! defined('DAY_IN_SECONDS')) {
        define('DAY_IN_SECONDS', 86400);
    }
    if (! defined('EP_ROOT')) {
        define('EP_ROOT', 64);
    }
    if (! defined('EP_PAGES')) {
        define('EP_PAGES', 4096);
    }
    if (! defined('RETURNS_DIR')) {
        define('RETURNS_DIR', '/tmp/returns/');
    }
    if (! defined('RETURNS_URL')) {
        define('RETURNS_URL', 'https://example.test/wp-content/plugins/returns/');
    }
}

namespace Returns {
    if (! defined('Returns\\VERSION')) {
        define('Returns\\VERSION', '0.1.0');
    }
    if (! defined('Returns\\PLUGIN_FILE')) {
        define('Returns\\PLUGIN_FILE', '/tmp/returns/returns.php');
    }
}
