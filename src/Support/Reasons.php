<?php

declare(strict_types=1);

namespace Returns\Support;

defined('ABSPATH') || exit;

/**
 * Built-in return reasons and labels shared by the storefront form and PRO analytics.
 */
final class Reasons
{
    /**
     * @return array<string, string> reason slug => translated label
     */
    public static function all(): array
    {
        $reasons = [
            'damaged'    => __('Arrived damaged or faulty', 'plogins-returns'),
            'wrong_item' => __('Wrong item received', 'plogins-returns'),
            'not_needed' => __('No longer needed', 'plogins-returns'),
            'size_fit'   => __('Size or fit issue', 'plogins-returns'),
            'other'      => __('Other', 'plogins-returns'),
        ];

        /**
         * Filters the list of return reasons shown on the customer form.
         *
         * @param array<string, string> $reasons Reason slug => label.
         */
        return (array) apply_filters('returns/reasons', $reasons);
    }

    public static function label(string $slug): string
    {
        $reasons = self::all();
        $label   = $reasons[$slug] ?? $slug;

        /**
         * Filters the display label for a return reason slug.
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
}
