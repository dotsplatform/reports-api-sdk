<?php

declare(strict_types=1);

/**
 * Description of MetricsHttpClientTest.php
 *
 * metrics(): catalogue, dimensions, scopes with a cursor, POST metrics/query (request body and every value shape:
 * count, money, ratio, mean in seconds, breakdown, top list, deltas) and raw snapshots.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\DTO\Common\MoneyDTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\PeriodRequestDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\QueryMetricsRequestDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\SeriesRequestDTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\PeriodPreset;
use Dotsplatform\ReportsApi\Enums\ReportLocale;
use Dotsplatform\ReportsApi\Enums\ScopeType;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;

class MetricsHttpClientTest extends HttpClientTestCase
{
    private const string ACCOUNT = StubResponses::ACCOUNT_ID;

    public function testListsTheMetricCatalogueInALocale(): void
    {
        $this->queueJsonResponse(['data' => [StubResponses::metricCatalogueEntry()]]);

        $metrics = $this->makeClient()->metrics()->listMetrics(self::ACCOUNT, ReportLocale::UA);

        $this->assertLastRequest('GET', '/api/v1/accounts/'.self::ACCOUNT.'/metrics');
        $this->assertSame(['locale' => 'ua'], $this->getLastRequestQuery());
        $this->assertCount(1, $metrics);
        $this->assertSame('orders.count', $metrics[0]->getKey());
        $this->assertSame('Orders', $metrics[0]->getLabel());
        $this->assertSame('up', $metrics[0]->getGoodDirection());
        $this->assertTrue($metrics[0]->isMergeable());
        $this->assertTrue($metrics[0]->isAvailable());
        $this->assertSame(1, $metrics[0]->getVersion());
    }

    public function testListsDimensionsWithoutALocale(): void
    {
        $this->queueJsonResponse(['data' => [StubResponses::dimension()]]);

        $dimensions = $this->makeClient()->metrics()->listDimensions(self::ACCOUNT);

        $this->assertLastRequest('GET', '/api/v1/accounts/'.self::ACCOUNT.'/dimensions');
        $this->assertSame([], $this->getLastRequestQuery());
        $this->assertSame('delivery_type', $dimensions[0]->getKey());
        $this->assertSame('Delivery type', $dimensions[0]->getLabel());
    }

    public function testListsScopesPageByPage(): void
    {
        $firstPage = StubResponses::scopesPage();
        $firstPage['next_cursor'] = 'cursor-2';
        $this->queueJsonResponse($firstPage);
        $this->queueJsonResponse(StubResponses::scopesPage());

        $page = $this->makeClient()->metrics()->listScopes(self::ACCOUNT, null, 2);
        $this->assertSame(['limit' => '2'], $this->getLastRequestQuery());
        $this->assertTrue($page->hasNextPage());
        $this->assertSame('cursor-2', $page->getNextCursor());
        $this->assertSame(ScopeType::ACCOUNT, $page->getScopes()[0]->getScopeType());
        $this->assertSame(StubResponses::CITY_ID, $page->getScopes()[1]->getScopeId());
        $this->assertSame(self::ACCOUNT, $page->getScopes()[1]->getParentId());
        $this->assertSame(ScopeType::CITY, $page->getScopes()[1]->toScope()->getScopeType());

        $lastPage = $this->makeClient()->metrics()->listScopes(self::ACCOUNT, 'cursor-2', 2);
        $this->assertSame(['cursor' => 'cursor-2', 'limit' => '2'], $this->getLastRequestQuery());
        $this->assertFalse($lastPage->hasNextPage());
        $this->assertNull($lastPage->getNextCursor());
    }

    public function testQuerySendsTheBody(): void
    {
        $this->queueJsonResponse(StubResponses::queryMetricsResult());

        $this->makeClient()->metrics()->queryMetrics(self::ACCOUNT, QueryMetricsRequestDTO::fromArray([
            'metricKeys' => ['orders.count', 'orders.revenue'],
            'scope' => ScopeDTO::create(ScopeType::CITY, StubResponses::CITY_ID),
            'periods' => [PeriodRequestDTO::month('2026-09'), PeriodRequestDTO::custom('2026-10-01', '2026-10-08')],
            'compare' => CompareMode::PREVIOUS_PERIOD,
        ]));

        $this->assertLastRequest('POST', '/api/v1/accounts/'.self::ACCOUNT.'/metrics/query');
        $this->assertSame([
            'metrics' => ['orders.count', 'orders.revenue'],
            'scope' => ['type' => 'city', 'id' => StubResponses::CITY_ID],
            'periods' => [
                ['preset' => 'month', 'key' => '2026-09'],
                ['preset' => 'custom', 'from' => '2026-10-01', 'to' => '2026-10-08'],
            ],
            'compare' => 'previous_period',
        ], $this->getLastRequestJson());
    }

    public function testQuerySendsASeriesOrOnePeriod(): void
    {
        $this->queueJsonResponse(StubResponses::queryMetricsResult());
        $this->queueJsonResponse(StubResponses::queryMetricsResult());
        $scope = ScopeDTO::create(ScopeType::ACCOUNT, self::ACCOUNT);

        $this->makeClient()->metrics()->queryMetrics(self::ACCOUNT, QueryMetricsRequestDTO::fromArray([
            'metricKeys' => ['orders.count'],
            'scope' => $scope,
            'series' => SeriesRequestDTO::last(6, Grain::MONTH),
        ]));
        $this->assertSame(['grain' => 'month', 'last' => 6], $this->getLastRequestJson()['series'] ?? null);
        $this->assertArrayNotHasKey('period', $this->getLastRequestJson());
        $this->assertArrayNotHasKey('compare', $this->getLastRequestJson());

        $this->makeClient()->metrics()->queryMetrics(self::ACCOUNT, QueryMetricsRequestDTO::fromArray([
            'metricKeys' => ['orders.count'],
            'scope' => $scope,
            'period' => PeriodRequestDTO::preset(PeriodPreset::THIS_MONTH),
        ]));
        $this->assertSame(['preset' => 'this_month'], $this->getLastRequestJson()['period'] ?? null);
    }

    public function testQueryMapsMoneyRatiosAndDurations(): void
    {
        $this->queueJsonResponse(StubResponses::queryMetricsResult());

        $result = $this->makeClient()->metrics()->queryMetrics(self::ACCOUNT, $this->makeAccountQuery());

        $this->assertSame(self::ACCOUNT, $result->getAccountId());
        $this->assertSame('UAH', $result->getCurrency());
        $this->assertSame(ScopeType::ACCOUNT, $result->getScope()->getScopeType());
        $period = $result->getPeriods()[0];
        $this->assertSame('month', $period->getPeriod()->getGrain());
        $this->assertSame('2026-09', $period->getPeriod()->getKey());
        $this->assertSame('2026-09-01', $period->getPeriod()->getFromDate());
        $this->assertSame('2026-09-30', $period->getPeriod()->getToDate());
        $this->assertSame('2026-10-01T03:00:00Z', $period->getDataAsOf());

        $ordersCount = $period->getMetric('orders.count');
        $this->assertNotNull($ordersCount);
        $this->assertSame(2, $ordersCount->getNumber());
        $this->assertNull($ordersCount->getMoney());
        $this->assertNull($ordersCount->getRatio());

        $revenue = $period->getMetric('orders.revenue');
        $this->assertNotNull($revenue);
        $this->assertTrue($revenue->isMoney());
        $this->assertSame(80000, $revenue->getNumber());
        $this->assertEquals(MoneyDTO::fromArray(['amountCents' => 80000, 'currency' => 'UAH']), $revenue->getMoney());

        $averageCheck = $period->getMetric('orders.avg_check');
        $this->assertNotNull($averageCheck);
        $averageCheckMoney = $averageCheck->getMoney();
        $this->assertNotNull($averageCheckMoney);
        $this->assertSame(40000, $averageCheckMoney->getAmountCents());
        $averageCheckRatio = $averageCheck->getRatio();
        $this->assertNotNull($averageCheckRatio);
        $this->assertSame(80000, $averageCheckRatio->getNumerator());
        $this->assertSame(2, $averageCheckRatio->getDenominator());
        $this->assertSame(40000, $averageCheckRatio->getValue());

        $leadTime = $period->getMetric('lead_time.avg');
        $this->assertNotNull($leadTime);
        $this->assertSame('seconds', $leadTime->getUnit());
        $this->assertSame(1800, $leadTime->getNumber());
        $this->assertSame(3600, $leadTime->getRatio()?->getNumerator());
    }

    public function testQueryMapsBreakdownsAndTopLists(): void
    {
        $this->queueJsonResponse(StubResponses::queryMetricsResult());

        $period = $this->makeClient()->metrics()->queryMetrics(self::ACCOUNT, $this->makeAccountQuery())->getPeriods()[0];

        $byDeliveryType = $period->getMetric('orders.by_delivery_type');
        $this->assertNotNull($byDeliveryType);
        $this->assertTrue($byDeliveryType->isBreakdown());
        $this->assertNull($byDeliveryType->getNumber());
        $breakdown = $byDeliveryType->getBreakdown() ?? [];
        $this->assertSame(['delivery', 'pickup'], array_keys($breakdown));
        $this->assertSame(2, $breakdown['delivery']->getNumber());
        $this->assertSame('breakdown', $breakdown['delivery']->getKind());
        $this->assertSame('count', $breakdown['pickup']->getUnit());

        $topItems = $period->getMetric('items.top');
        $this->assertNotNull($topItems);
        $this->assertTrue($topItems->isTopList());
        $this->assertNull($topItems->getUnit());
        $entry = ($topItems->getTopEntries() ?? [])[0];
        $this->assertSame('Philadelphia roll', $entry->getLabel());
        $this->assertSame(2, $entry->getNumber('units'));
        $entryRevenue = $entry->getMoney('revenue_cents');
        $this->assertNotNull($entryRevenue);
        $this->assertSame(80000, $entryRevenue->getAmountCents());
        $this->assertSame('UAH', $entryRevenue->getCurrency());
        $this->assertSame(80000, $entry->getNumber('revenue_cents'));
        $this->assertNull($entry->getMoney('units'));
    }

    public function testQueryMapsTheComparisonAndDeltas(): void
    {
        $this->queueJsonResponse(StubResponses::queryMetricsResult());

        $period = $this->makeClient()->metrics()->queryMetrics(self::ACCOUNT, $this->makeAccountQuery())->getPeriods()[0];

        $comparison = $period->getComparison();
        $this->assertNotNull($comparison);
        $this->assertSame('2026-08-01', $comparison->getFromDate());
        $this->assertSame('2026-08-31', $comparison->getToDate());
        $this->assertSame(1, $comparison->getMetric('orders.count')?->getNumber());
        $ordersDelta = $comparison->getDelta('orders.count');
        $this->assertNotNull($ordersDelta);
        $this->assertSame(1, $ordersDelta->getAbsolute());
        $this->assertSame(1, $ordersDelta->getRelative());
        $this->assertNull($ordersDelta->getAbsoluteMoney());
        $revenueDelta = $comparison->getDelta('orders.revenue');
        $this->assertNotNull($revenueDelta);
        $this->assertSame(50000, $revenueDelta->getAbsolute());
        $this->assertSame('UAH', $revenueDelta->getAbsoluteMoney()?->getCurrency());
        $this->assertSame(1.6667, $revenueDelta->getRelative());
        $this->assertNull($comparison->getDelta('customers.unique'));
    }

    public function testAPeriodWithoutComparisonOrValue(): void
    {
        $this->queueJsonResponse([
            'account_id' => self::ACCOUNT,
            'currency' => 'USD',
            'scope' => ['type' => 'account', 'id' => self::ACCOUNT],
            'periods' => [[
                'grain' => 'custom',
                'key' => '2026-10-01..2026-10-08',
                'from' => '2026-10-01',
                'to' => '2026-10-08',
                'data_as_of' => null,
                'metrics' => ['orders.avg_check' => ['kind' => 'ratio', 'unit' => 'money', 'amount_cents' => null, 'currency' => 'USD', 'numerator' => 0, 'denominator' => 0]],
                'compare' => null,
            ]],
        ]);

        $period = $this->makeClient()->metrics()->queryMetrics(self::ACCOUNT, $this->makeAccountQuery())->getPeriods()[0];

        $this->assertSame('custom', $period->getPeriod()->getGrain());
        $this->assertNull($period->getDataAsOf());
        $this->assertNull($period->getComparison());
        $averageCheck = $period->getMetric('orders.avg_check');
        $this->assertNotNull($averageCheck);
        $this->assertNull($averageCheck->getNumber());
        $this->assertNull($averageCheck->getMoney());
        $averageCheckRatio = $averageCheck->getRatio();
        $this->assertNotNull($averageCheckRatio);
        $this->assertSame(0, $averageCheckRatio->getDenominator());
        $this->assertNull($averageCheckRatio->getValue());
    }

    public function testGetsTheAccountSnapshot(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::snapshot()]);

        $snapshot = $this->makeClient()->metrics()->getSnapshot(self::ACCOUNT, Grain::MONTH, '2026-09');

        $this->assertLastRequest('GET', '/api/v1/accounts/'.self::ACCOUNT.'/snapshots/month/2026-09');
        $this->assertSame([], $this->getLastRequestQuery());
        $this->assertSame(2, $snapshot->getOrdersCount());
        $this->assertSame('UAH', $snapshot->getCurrency());
        $this->assertSame('2026-09-01', $snapshot->getPeriod()->getFromDate());
        $this->assertSame(StubResponses::CREATED_AT, $snapshot->getComputedAt());
        $this->assertSame('a1b2c3', $snapshot->getMeasuresVersion());
        $this->assertSame(80000, $snapshot->getPayload()['full_price_cents'] ?? null);
    }

    public function testGetsTheSnapshotOfAScope(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::snapshot()]);

        $this->makeClient()->metrics()->getSnapshot(self::ACCOUNT, Grain::ISO_WEEK, '2026-W41', ScopeDTO::create(ScopeType::CITY, StubResponses::CITY_ID));

        $this->assertLastRequest('GET', '/api/v1/accounts/'.self::ACCOUNT.'/snapshots/iso_week/2026-W41');
        $this->assertSame(['scope_type' => 'city', 'scope_id' => StubResponses::CITY_ID], $this->getLastRequestQuery());
    }

    private function makeAccountQuery(): QueryMetricsRequestDTO
    {
        return QueryMetricsRequestDTO::fromArray([
            'metricKeys' => ['orders.count'],
            'scope' => ScopeDTO::create(ScopeType::ACCOUNT, self::ACCOUNT),
            'period' => PeriodRequestDTO::month('2026-09'),
        ]);
    }
}
