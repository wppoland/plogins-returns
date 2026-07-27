<?php
/**
 * PRO upsell content, generated from the plogins.com registry by
 * scripts/gen-pro-upsell.mjs. The admin upsell renders this; curate the
 * feature list to fit this plugin's settings screen (do not invent features).
 *
 * @package plogins-returns-pro
 */

defined('ABSPATH') || exit;

return [
    'name'       => 'Returns Pro',
    'url'        => 'https://plogins.com/plogins-returns-pro/pricing/',
    'sellable'   => true,
    'price_from' => 29,
    'currency'   => 'EUR',
    'price_pln'  => 129,
    'lead'       => [
        'en' => 'Status emails, return shipping labels, automatic store credit, return-reason analytics and refund actions ship in version 0.6.0. Feature-complete PRO.',
        'pl' => 'Wydanie 0.6.0 dostarcza e-maile statusu, etykiety zwrotne, automatyczny kredyt sklepowy, analitykę powodów i akcje zwrotu środków. Feature-complete PRO.',
    ],
    'features'   => [
        [
            'en' => ['title' => 'Status-change emails', 'desc' => 'Notify the customer when a return is approved, rejected or completed (shipped).'],
            'pl' => ['title' => 'E-maile o statusie', 'desc' => 'Powiadom klienta, gdy zgłoszenie zostanie zatwierdzone, odrzucone lub zakończone (wdrożone).'],
        ],
        [
            'en' => ['title' => 'Refund amount line', 'desc' => 'Include the refund amount in the completed-status email (shipped).'],
            'pl' => ['title' => 'Kwota zwrotu w e-mailu', 'desc' => 'Dołącz kwotę zwrotu w wiadomości o statusie Completed (wdrożone).'],
        ],
        [
            'en' => ['title' => 'Store credit line', 'desc' => 'Include store-credit code and amount in the completed email (shipped).'],
            'pl' => ['title' => 'Kredyt sklepowy w e-mailu', 'desc' => 'Dołącz kod i kwotę kredytu sklepowego w wiadomości o zakończeniu (wdrożone).'],
        ],
        [
            'en' => ['title' => 'Return shipping labels', 'desc' => 'Generate return shipping labels for approved requests (ShippingLabels, shipped).'],
            'pl' => ['title' => 'Etykiety zwrotnej wysyłki', 'desc' => 'Generuj etykiety zwrotne dla zatwierdzonych zgłoszeń (ShippingLabels, wdrożone).'],
        ],
        [
            'en' => ['title' => 'Automatic store credit', 'desc' => 'Issue store credit automatically when a return is completed (AutoStoreCredit, shipped).'],
            'pl' => ['title' => 'Automatyczny kredyt sklepowy', 'desc' => 'Wystaw kredyt sklepowy automatycznie po zakończeniu zwrotu (AutoStoreCredit, wdrożone).'],
        ],
        [
            'en' => ['title' => 'Return-reason analytics', 'desc' => 'A dashboard of return reasons, rates and trends to spot the products and causes driving returns (ReturnReasonAnalytics, shipped).'],
            'pl' => ['title' => 'Analityka powodów', 'desc' => 'Pulpit powodów, trendów i linii produktowych napędzających zwroty (ReturnReasonAnalytics, wdrożone).'],
        ],
        [
            'en' => ['title' => 'Refund actions', 'desc' => 'Process WooCommerce refunds from the return request screen (RefundActions, shipped in 0.6.0).'],
            'pl' => ['title' => 'Akcje zwrotu środków', 'desc' => 'Przetwarzaj zwroty WooCommerce z ekranu zgłoszenia zwrotu (RefundActions, wdrożone w 0.6.0).'],
        ],
    ],
];
