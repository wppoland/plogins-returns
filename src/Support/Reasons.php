<?php

declare(strict_types=1);

namespace Returns\Support;

defined('ABSPATH') || exit;

/**
 * Built-in request reasons and labels shared by the storefront form and PRO
 * analytics. Reasons are type-aware: a plain return, a complaint and a repair
 * request each offer their own list, so the customer only sees reasons that fit
 * the kind of request they are opening.
 */
final class Reasons
{
    /**
     * The reason sets keyed by request type. RETURN keeps the original list for
     * back-compat; complaint and repair add their own fault-focused reasons.
     *
     * @return array<string, array<string, string>>
     */
    private static function sets(): array
    {
        return [
            Types::RETURN => [
                'damaged'    => __('Arrived damaged or faulty', 'redono'),
                'wrong_item' => __('Wrong item received', 'redono'),
                'not_needed' => __('No longer needed', 'redono'),
                'size_fit'   => __('Size or fit issue', 'redono'),
                'other'      => __('Other', 'redono'),
            ],
            Types::COMPLAINT => [
                'defective'          => __('Defective or not working', 'redono'),
                'not_as_described'   => __('Not as described', 'redono'),
                'damaged_in_transit' => __('Damaged in transit', 'redono'),
                'missing_parts'      => __('Missing parts or accessories', 'redono'),
                'other'              => __('Other', 'redono'),
            ],
            Types::REPAIR => [
                'stopped_working'    => __('Stopped working', 'redono'),
                'intermittent_fault' => __('Intermittent fault', 'redono'),
                'physical_damage'    => __('Physical damage', 'redono'),
                'wont_power_on'      => __('Will not power on', 'redono'),
                'other'              => __('Other', 'redono'),
            ],
        ];
    }

    /**
     * The reasons offered for a given request type.
     *
     * @return array<string, string> reason slug => translated label
     */
    public static function forType(string $type): array
    {
        $sets    = self::sets();
        $type    = isset($sets[$type]) ? $type : Types::RETURN;
        $reasons = $sets[$type];

        /**
         * Filters the list of reasons shown for a request type on the form.
         *
         * @param array<string, string> $reasons Reason slug => label.
         * @param string                $type    The request type key.
         */
        return (array) apply_filters('returns/reasons', $reasons, $type);
    }

    /**
     * Every reason across every type, merged for label and validity lookups.
     *
     * @return array<string, string> reason slug => translated label
     */
    public static function all(): array
    {
        $all = [];

        foreach (array_keys(self::sets()) as $type) {
            $all = array_merge($all, self::forType($type));
        }

        return $all;
    }

    public static function label(string $slug): string
    {
        $reasons = self::all();
        $label   = $reasons[$slug] ?? $slug;

        /**
         * Filters the display label for a request reason slug.
         *
         * @param string $label Translated or fallback label.
         * @param string $slug  Reason slug.
         */
        return (string) apply_filters('returns/reason_label', $label, $slug);
    }

    public static function isValid(string $slug): bool
    {
        return array_key_exists($slug, self::all());
    }

    /**
     * Whether a reason belongs to the given request type. Used to server-side
     * validate that the submitted reason matches the chosen type.
     */
    public static function isValidForType(string $slug, string $type): bool
    {
        return array_key_exists($slug, self::forType($type));
    }
}
