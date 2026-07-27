<?php

declare(strict_types=1);

namespace Returns\Support;

defined('ABSPATH') || exit;

/**
 * Canonical list of request types and the preferred remedies that go with them.
 *
 * A request is one of three kinds: a plain return (send an item back for a
 * refund or exchange), a complaint (report a fault or warranty issue), or a
 * repair request (the EU Right to Repair path). The type is stored as a post
 * meta value on the return CPT. Records created before typing existed have no
 * type meta and read back as RETURN, so old data keeps working unchanged.
 *
 * For a complaint or a repair the customer can also state a preferred remedy
 * (repair, replacement or refund). Return requests carry no remedy.
 */
final class Types
{
    public const RETURN    = 'return';
    public const COMPLAINT = 'complaint';
    public const REPAIR    = 'repair';

    public const REMEDY_REPAIR      = 'repair';
    public const REMEDY_REPLACEMENT = 'replacement';
    public const REMEDY_REFUND      = 'refund';

    /**
     * All request types, RETURN first so it stays the default. The RETURN type
     * must remain the first entry for back-compat.
     *
     * @return array<string, string> type key => translated label
     */
    public static function all(): array
    {
        $types = [
            self::RETURN    => __('Return', 'plogins-returns'),
            self::COMPLAINT => __('Complaint', 'plogins-returns'),
            self::REPAIR    => __('Repair', 'plogins-returns'),
        ];

        /**
         * Filters the list of request types shown on the customer form.
         *
         * Add-ons (e.g. Returns Pro) can extend this to offer more request
         * kinds. Keep RETURN first so it stays the default.
         *
         * @param array<string, string> $types Type key => label.
         */
        return (array) apply_filters('returns/types', $types);
    }

    public static function label(string $type): string
    {
        $all = self::all();

        return $all[$type] ?? $all[self::RETURN];
    }

    public static function isValid(string $type): bool
    {
        return array_key_exists($type, self::all());
    }

    /**
     * A CSS-safe modifier suffix for a type, for storefront/admin badges.
     */
    public static function slug(string $type): string
    {
        return self::isValid($type) ? $type : self::RETURN;
    }

    /**
     * A short helper sentence describing what each request type is for.
     */
    public static function description(string $type): string
    {
        $descriptions = [
            self::RETURN    => __('Send an item back for a refund or exchange.', 'plogins-returns'),
            self::COMPLAINT => __('Report a fault or a warranty issue with your order.', 'plogins-returns'),
            self::REPAIR    => __('Ask us to repair a faulty item (your right to repair).', 'plogins-returns'),
        ];

        return $descriptions[$type] ?? '';
    }

    /**
     * Whether a type expects a preferred remedy (complaint and repair do).
     */
    public static function hasRemedy(string $type): bool
    {
        return in_array($type, [self::COMPLAINT, self::REPAIR], true);
    }

    /**
     * The preferred-remedy choices offered for complaint and repair requests.
     *
     * @return array<string, string> remedy key => translated label
     */
    public static function remedies(): array
    {
        return [
            self::REMEDY_REPAIR      => __('Repair', 'plogins-returns'),
            self::REMEDY_REPLACEMENT => __('Replacement', 'plogins-returns'),
            self::REMEDY_REFUND      => __('Refund', 'plogins-returns'),
        ];
    }

    public static function isValidRemedy(string $remedy): bool
    {
        return array_key_exists($remedy, self::remedies());
    }

    public static function remedyLabel(string $remedy): string
    {
        $remedies = self::remedies();

        return $remedies[$remedy] ?? $remedy;
    }
}
