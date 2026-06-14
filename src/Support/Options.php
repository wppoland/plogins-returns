<?php

declare(strict_types=1);

namespace Returns\Support;

defined('ABSPATH') || exit;

/**
 * Typed accessor for the plugin's settings, merging stored values over the
 * shipped defaults. Centralises option reads so every service sees the same
 * shape and sane fallbacks even when the option is missing or malformed.
 */
final class Options
{
    public const OPTION = 'returns_settings';

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $stored = get_option(self::OPTION, []);

        if (! is_array($stored)) {
            $stored = [];
        }

        /** @var array<string, mixed> $defaults */
        $defaults = require RETURNS_DIR . 'config/defaults.php';

        return array_merge($defaults, $stored);
    }

    public static function isEnabled(): bool
    {
        return (bool) (self::all()['enabled'] ?? false);
    }

    /**
     * @return list<string>
     */
    public static function eligibleStatuses(): array
    {
        $statuses = self::all()['eligible_statuses'] ?? [];

        if (! is_array($statuses)) {
            return ['completed'];
        }

        $clean = array_values(array_filter(array_map('strval', $statuses)));

        return [] === $clean ? ['completed'] : $clean;
    }

    public static function windowDays(): int
    {
        return max(0, (int) (self::all()['window_days'] ?? 0));
    }
}
