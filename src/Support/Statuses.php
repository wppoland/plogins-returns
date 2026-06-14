<?php

declare(strict_types=1);

namespace Returns\Support;

defined('ABSPATH') || exit;

/**
 * Canonical list of return-request statuses and their human-readable labels.
 *
 * Statuses are stored as a post meta value on the return CPT (not as a post
 * status) so they stay decoupled from WordPress publish states. The customer
 * sees the same label in My Account that the merchant sets in wp-admin.
 */
final class Statuses
{
    public const REQUESTED = 'requested';
    public const APPROVED  = 'approved';
    public const REJECTED  = 'rejected';
    public const COMPLETED = 'completed';

    /**
     * @return array<string, string> status key => translated label
     */
    public static function all(): array
    {
        return [
            self::REQUESTED => __('Requested', 'returns'),
            self::APPROVED  => __('Approved', 'returns'),
            self::REJECTED  => __('Rejected', 'returns'),
            self::COMPLETED => __('Completed', 'returns'),
        ];
    }

    public static function label(string $status): string
    {
        $all = self::all();

        return $all[$status] ?? $all[self::REQUESTED];
    }

    public static function isValid(string $status): bool
    {
        return array_key_exists($status, self::all());
    }

    /**
     * A CSS-safe modifier suffix for a status, for storefront/admin badges.
     */
    public static function slug(string $status): string
    {
        return self::isValid($status) ? $status : self::REQUESTED;
    }
}
