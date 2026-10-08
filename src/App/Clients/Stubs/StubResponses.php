<?php

declare(strict_types=1);

/**
 * Description of StubResponses.php
 *
 * Sample response bodies in the exact JSON shapes of the reports API v1. SuccessReportsStubClient builds its DTOs from
 * them through the same fromResponse() mapping as the HTTP client, and the SDK tests use them as mocked responses.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs;

final class StubResponses
{
    public const string ACCOUNT_ID = '0199a000-0000-7000-8000-000000000291';

    public const string CITY_ID = '0199a000-0000-7000-8000-00000000c001';

    public const string DEFINITION_ID = '0199a000-0000-7000-8000-0000000d0001';

    public const string RUN_ID = '0199a000-0000-7000-8000-0000000e0001';

    public const string API_CLIENT_ID = '0199a000-0000-7000-8000-0000000f0001';

    public const string SUBSCRIPTION_ID = '0199a000-0000-7000-8000-000000050001';

    public const string RECIPIENT_ID = '0199a000-0000-7000-8000-000000060001';

    public const string DELIVERY_ID = '0199a000-0000-7000-8000-000000070001';

    public const string CURRENCY = 'UAH';

    public const int CREATED_AT = 1791460800;

    public const string PDF_BYTES = "%PDF-1.7\n%stub\n";

    /**
     * @return array<string, mixed>
     */
    public static function metricCatalogueEntry(): array
    {
        return [
            'key' => 'orders.count',
            'label' => 'Orders',
            'definition' => 'Finished orders, test orders excluded.',
            'unit' => 'count',
            'kind' => 'count',
            'good_direction' => 'up',
            'mergeable' => true,
            'version' => 1,
            'available' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dimension(): array
    {
        return ['key' => 'delivery_type', 'label' => 'Delivery type'];
    }

    /**
     * @return array<string, mixed>
     */
    public static function scopesPage(): array
    {
        return [
            'data' => [
                ['type' => 'account', 'id' => self::ACCOUNT_ID, 'name' => 'Real Roll', 'parent_id' => null],
                ['type' => 'city', 'id' => self::CITY_ID, 'name' => 'Kyiv', 'parent_id' => self::ACCOUNT_ID],
            ],
            'next_cursor' => null,
        ];
    }

    /**
     * POST metrics/query is not wrapped in "data".
     *
     * @return array<string, mixed>
     */
    public static function queryMetricsResult(): array
    {
        return [
            'account_id' => self::ACCOUNT_ID,
            'currency' => self::CURRENCY,
            'scope' => ['type' => 'account', 'id' => self::ACCOUNT_ID],
            'periods' => [
                [
                    'grain' => 'month',
                    'key' => '2026-09',
                    'from' => '2026-09-01',
                    'to' => '2026-09-30',
                    'data_as_of' => '2026-10-01T03:00:00Z',
                    'metrics' => self::periodMetrics(2, 80000),
                    'compare' => [
                        'from' => '2026-08-01',
                        'to' => '2026-08-31',
                        'metrics' => self::periodMetrics(1, 30000),
                        'deltas' => [
                            'orders.count' => ['absolute' => 1, 'relative' => 1],
                            'orders.revenue' => ['absolute' => ['amount_cents' => 50000, 'currency' => self::CURRENCY], 'relative' => 1.6667],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function snapshot(): array
    {
        return [
            'scope' => ['type' => 'account', 'id' => self::ACCOUNT_ID],
            'period' => ['grain' => 'month', 'key' => '2026-09', 'from' => '2026-09-01', 'to' => '2026-09-30'],
            'currency' => self::CURRENCY,
            'orders_count' => 2,
            'computed_at' => self::CREATED_AT,
            'measures_version' => 'a1b2c3',
            'payload' => ['orders' => 2, 'full_price_cents' => 80000],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function blockType(): array
    {
        return [
            'type' => 'top_table',
            'family' => 'venues',
            'config_schema' => ['title' => 'string', 'entity' => 'string', 'limit' => 'integer'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function reportDefinition(string $definitionId = self::DEFINITION_ID, string $name = 'Monthly owner report'): array
    {
        return [
            'id' => $definitionId,
            'name' => $name,
            'is_template' => false,
            'template_key' => 'pro_monthly',
            'purpose' => 'owner',
            'scope' => ['type' => 'account', 'id' => null],
            'default_grain' => 'month',
            'comparison' => 'previous_period',
            'trend_periods' => 6,
            'share_link_days' => 90,
            'is_default' => true,
            'created_by' => 'livesite-user-7',
            'layout' => [
                'blocks' => [
                    ['type' => 'header', 'config' => []],
                    ['type' => 'kpi_group', 'config' => ['group' => 'general']],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function reportData(): array
    {
        return [
            'name' => 'Monthly owner report',
            'scope_name' => 'Real Roll',
            'locale' => 'en',
            'period' => ['grain' => 'month', 'key' => '2026-09', 'label' => 'September 2026'],
            'compared_with' => 'August 2026',
            'data_as_of' => self::CREATED_AT,
            'blocks' => [
                ['type' => 'header', 'title' => 'Real Roll', 'data' => ['orders' => '2']],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function reportRun(string $runId = self::RUN_ID, string $status = 'pdf_pending'): array
    {
        return [
            'id' => $runId,
            'definition_id' => self::DEFINITION_ID,
            'scope' => ['type' => 'account', 'id' => self::ACCOUNT_ID],
            'period' => ['grain' => 'month', 'key' => '2026-09'],
            'compare' => null,
            'is_anonymised' => false,
            'status' => $status,
            'has_share_link' => true,
            'expires_at' => self::CREATED_AT + 90 * 86400,
            'created_at' => self::CREATED_AT,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function createdReportRun(): array
    {
        return [
            'data' => self::reportRun(),
            'share_url' => 'https://reports.example.test/r/stub-share-token',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function shareLink(): array
    {
        return [
            'run_id' => self::RUN_ID,
            'url' => 'https://reports.example.test/r/stub-share-token',
            'expires_at' => self::CREATED_AT + 90 * 86400,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function reportSettings(): array
    {
        return [
            'locale' => 'ua',
            'timezone' => 'Europe/Kyiv',
            'currency' => self::CURRENCY,
            'currency_sign' => '₴',
            'currency_position' => 'suffix',
            'thousands_separator' => ' ',
            'decimal_separator' => ',',
            'value_bands_cents' => [20000, 50000],
            'distance_band_target_minutes' => [30, 40, 50, 60, 70, 80],
            'tariff_zone_target_minutes' => [],
            'outside_zones_target_minutes' => 90,
            'peak_hours' => [18, 20],
            'hidden_blocks' => [],
            'plan_profile' => 'pro',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function apiClient(string $name = 'Claude'): array
    {
        return [
            'id' => self::API_CLIENT_ID,
            'name' => $name,
            'abilities' => ['metrics:read', 'reports:read'],
            'created_by' => 'livesite-user-7',
            'created_at' => self::CREATED_AT,
            'last_used_at' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function issuedApiClient(string $name = 'Claude'): array
    {
        return [
            'data' => self::apiClient($name),
            'token' => '1|dotsr_stubplaintoken',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function subscription(string $subscriptionId = self::SUBSCRIPTION_ID): array
    {
        return [
            'id' => $subscriptionId,
            'definition_id' => self::DEFINITION_ID,
            'scope' => ['type' => 'account', 'id' => self::ACCOUNT_ID],
            'grain' => 'iso_week',
            'schedule' => ['send_time' => '09:00', 'send_weekday' => 1, 'send_month_day' => null],
            'channel' => 'email',
            'format' => 'digest',
            'locale' => null,
            'is_active' => true,
            'paused_reason' => null,
            'next_due_at' => self::CREATED_AT + 86400,
            'last_sent_at' => null,
            'last_period_key' => null,
            'created_by' => 'livesite-user-7',
            'recipients' => [
                ['id' => self::RECIPIENT_ID, 'email' => 'owner@example.test', 'name' => 'Owner', 'status' => 'active', 'unsubscribed_at' => null],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function delivery(): array
    {
        return [
            'id' => self::DELIVERY_ID,
            'recipient_id' => self::RECIPIENT_ID,
            'recipient_email' => 'owner@example.test',
            'send_key' => self::SUBSCRIPTION_ID.':2026-W41',
            'run_id' => self::RUN_ID,
            'channel' => 'email',
            'status' => 'delivered',
            'error' => null,
            'sent_at' => self::CREATED_AT,
            'delivered_at' => self::CREATED_AT + 5,
            'created_at' => self::CREATED_AT,
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function periodMetrics(int $ordersCount, int $revenueCents): array
    {
        return [
            'orders.count' => ['kind' => 'count', 'unit' => 'count', 'value' => $ordersCount],
            'orders.revenue' => ['kind' => 'sum', 'unit' => 'money', 'amount_cents' => $revenueCents, 'currency' => self::CURRENCY],
            'orders.avg_check' => [
                'kind' => 'ratio',
                'unit' => 'money',
                'amount_cents' => intdiv($revenueCents, $ordersCount),
                'currency' => self::CURRENCY,
                'numerator' => $revenueCents,
                'denominator' => $ordersCount,
            ],
            'lead_time.avg' => ['kind' => 'mean', 'unit' => 'seconds', 'seconds' => 1800, 'numerator' => 1800 * $ordersCount, 'denominator' => $ordersCount],
            'orders.by_delivery_type' => [
                'kind' => 'breakdown',
                'unit' => 'count',
                'values' => [
                    'delivery' => ['value' => $ordersCount],
                    'pickup' => ['value' => 0],
                ],
            ],
            'items.top' => [
                'kind' => 'top_n',
                'entries' => [
                    [
                        'id' => '0199a000-0000-7000-8000-0000000a0001',
                        'label' => 'Philadelphia roll',
                        'values' => ['units' => $ordersCount, 'revenue_cents' => ['amount_cents' => $revenueCents, 'currency' => self::CURRENCY]],
                    ],
                ],
            ],
        ];
    }
}
