<?php

declare(strict_types=1);

namespace Returns;

defined('ABSPATH') || exit;

/**
 * Idempotent schema/version migrations, run on every boot. Compares a stored
 * option against VERSION and applies forward steps as needed.
 */
final class Migrator
{
    private const OPTION = 'returns_db_version';

    public function maybeMigrate(): void
    {
        $current = (string) get_option(self::OPTION, '0');

        if (version_compare($current, VERSION, '>=')) {
            return;
        }

        // Seed default settings on first run so the storefront flow is active
        // immediately, without overwriting a merchant's saved configuration.
        if (false === get_option(\Returns\Support\Options::OPTION, false)) {
            /** @var array<string, mixed> $defaults */
            $defaults = require RETURNS_DIR . 'config/defaults.php';
            update_option(\Returns\Support\Options::OPTION, $defaults, false);
        }

        update_option(self::OPTION, VERSION, false);
    }
}
