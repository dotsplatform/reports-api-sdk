<?php

declare(strict_types=1);

/**
 * Description of StubClientsTest.php
 *
 * SuccessReportsStubClient answers every group with typed sample data; FailReportsStubClient throws
 * ReportsServerException (503) from every method.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Stubs;

use Dotsplatform\ReportsApi\Clients\FailReportsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Fail\StubFailure;
use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Clients\SuccessReportsStubClient;
use Dotsplatform\ReportsApi\DTO\ApiClients\Requests\CreateApiClientRequestDTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\PeriodRequestDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\QueryMetricsRequestDTO;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\BackfillRequestDTO;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\ReaggregationRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDefinitionFormDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\CreateReportRunRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\ReportPeriodRequestDTO;
use Dotsplatform\ReportsApi\DTO\Settings\Requests\UpdateReportSettingsDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\SubscriptionFormDTO;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ScopeType;
use Dotsplatform\ReportsApi\Enums\SubscriptionChannel;
use Dotsplatform\ReportsApi\Exceptions\ReportsServerException;
use Dotsplatform\ReportsApi\ReportsClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StubClientsTest extends TestCase
{
    private const string ACCOUNT = StubResponses::ACCOUNT_ID;

    private const string DEFINITION = StubResponses::DEFINITION_ID;

    public function testTheSuccessStubAnswersMetrics(): void
    {
        $metrics = (new SuccessReportsStubClient())->metrics();

        $this->assertSame('orders.count', $metrics->listMetrics(self::ACCOUNT)[0]->getKey());
        $this->assertSame('delivery_type', $metrics->listDimensions(self::ACCOUNT)[0]->getKey());
        $this->assertCount(2, $metrics->listScopes(self::ACCOUNT)->getScopes());
        $this->assertSame(2, $metrics->getSnapshot(self::ACCOUNT, Grain::MONTH, '2026-09')->getOrdersCount());

        $result = $metrics->queryMetrics(self::ACCOUNT, self::makeQuery());
        $this->assertSame(80000, $result->getPeriods()[0]->getMetric('orders.revenue')?->getMoney()?->getAmountCents());
    }

    public function testTheSuccessStubAnswersReportDefinitions(): void
    {
        $reportDefinitions = (new SuccessReportsStubClient())->reportDefinitions();
        $form = ReportDefinitionFormDTO::fromArray(['name' => 'Mine']);

        $this->assertCount(1, $reportDefinitions->list(self::ACCOUNT));
        $this->assertSame('Mine', $reportDefinitions->create(self::ACCOUNT, $form)->getName());
        $this->assertSame('Mine', $reportDefinitions->update(self::ACCOUNT, self::DEFINITION, $form)->getName());
        $this->assertSame('Monthly owner report', $reportDefinitions->duplicate(self::ACCOUNT, self::DEFINITION)->getName());
        $this->assertSame(self::DEFINITION, $reportDefinitions->get(self::ACCOUNT, self::DEFINITION)->getId());
        $this->assertSame(self::DEFINITION, $reportDefinitions->resetToTemplate(self::ACCOUNT, self::DEFINITION)->getId());
        $this->assertSame('header', $reportDefinitions->getData(self::ACCOUNT, self::DEFINITION)->getBlocks()[0]->getType());
        $this->assertSame('top_table', $reportDefinitions->listBlocks(self::ACCOUNT)[0]->getType());
        $reportDefinitions->delete(self::ACCOUNT, self::DEFINITION);
    }

    public function testTheSuccessStubAnswersRunsSettingsSubscriptionsApiClientsAndOperations(): void
    {
        $reportsClient = new SuccessReportsStubClient();
        $period = ReportPeriodRequestDTO::fromArray(['grain' => Grain::MONTH, 'periodKey' => '2026-09']);

        $this->assertNotNull($reportsClient->reportRuns()->create(self::ACCOUNT, self::DEFINITION, CreateReportRunRequestDTO::fromArray(['grain' => Grain::MONTH, 'periodKey' => '2026-09']))->getShareUrl());
        $this->assertTrue($reportsClient->reportRuns()->get(self::ACCOUNT, self::DEFINITION, StubResponses::RUN_ID)->isPdfReady());
        $this->assertCount(1, $reportsClient->reportRuns()->list(self::ACCOUNT, self::DEFINITION));
        $this->assertSame(StubResponses::PDF_BYTES, $reportsClient->reportRuns()->downloadPdf(self::ACCOUNT, self::DEFINITION, StubResponses::RUN_ID));
        $this->assertSame(StubResponses::RUN_ID, $reportsClient->reportRuns()->createShareLink(self::ACCOUNT, self::DEFINITION, $period)->getRunId());

        $this->assertSame('UAH', $reportsClient->settings()->get(self::ACCOUNT)->getCurrency());
        $this->assertSame('UAH', $reportsClient->settings()->update(self::ACCOUNT, UpdateReportSettingsDTO::empty())->getCurrency());

        $this->assertSame('Script', $reportsClient->apiClients()->create(self::ACCOUNT, CreateApiClientRequestDTO::create('Script'))->getApiClient()->getName());
        $this->assertCount(1, $reportsClient->apiClients()->list(self::ACCOUNT));
        $reportsClient->apiClients()->revoke(self::ACCOUNT, StubResponses::API_CLIENT_ID);

        $subscriptions = $reportsClient->subscriptions();
        $subscriptionId = StubResponses::SUBSCRIPTION_ID;
        $this->assertCount(1, $subscriptions->list(self::ACCOUNT));
        $this->assertSame($subscriptionId, $subscriptions->create(self::ACCOUNT, SubscriptionFormDTO::empty())->getId());
        $telegramForm = SubscriptionFormDTO::fromArray(['channel' => SubscriptionChannel::TELEGRAM]);
        $this->assertSame(SubscriptionChannel::TELEGRAM, $subscriptions->create(self::ACCOUNT, $telegramForm)->getChannel());
        $this->assertSame($subscriptionId, $subscriptions->get(self::ACCOUNT, $subscriptionId)->getId());
        $this->assertSame($subscriptionId, $subscriptions->update(self::ACCOUNT, $subscriptionId, SubscriptionFormDTO::empty())->getId());
        $this->assertSame('queued', $subscriptions->sendNow(self::ACCOUNT, $subscriptionId));
        $this->assertCount(1, $subscriptions->listDeliveries(self::ACCOUNT, $subscriptionId));
        $subscriptions->delete(self::ACCOUNT, $subscriptionId);

        $this->assertCount(1, $reportsClient->operations()->listTemplates());
        $this->assertSame(1, $reportsClient->operations()->queueBackfill(self::ACCOUNT, BackfillRequestDTO::empty()));
        $this->assertSame('queued', $reportsClient->operations()->queueReaggregation(ReaggregationRequestDTO::empty()));
    }

    /**
     * @return iterable<string, array{callable(ReportsClient): mixed}>
     */
    public static function everyCall(): iterable
    {
        $account = self::ACCOUNT;
        $definition = self::DEFINITION;
        $run = StubResponses::RUN_ID;
        $period = ReportPeriodRequestDTO::fromArray(['grain' => Grain::MONTH, 'periodKey' => '2026-09']);
        $form = ReportDefinitionFormDTO::empty();

        yield 'metrics.listMetrics' => [fn (ReportsClient $client): mixed => $client->metrics()->listMetrics($account)];
        yield 'metrics.listDimensions' => [fn (ReportsClient $client): mixed => $client->metrics()->listDimensions($account)];
        yield 'metrics.listScopes' => [fn (ReportsClient $client): mixed => $client->metrics()->listScopes($account)];
        yield 'metrics.queryMetrics' => [fn (ReportsClient $client): mixed => $client->metrics()->queryMetrics($account, self::makeQuery())];
        yield 'metrics.getSnapshot' => [fn (ReportsClient $client): mixed => $client->metrics()->getSnapshot($account, Grain::DAY, '2026-10-08')];
        yield 'reportDefinitions.list' => [fn (ReportsClient $client): mixed => $client->reportDefinitions()->list($account)];
        yield 'reportDefinitions.create' => [fn (ReportsClient $client): mixed => $client->reportDefinitions()->create($account, $form)];
        yield 'reportDefinitions.get' => [fn (ReportsClient $client): mixed => $client->reportDefinitions()->get($account, $definition)];
        yield 'reportDefinitions.update' => [fn (ReportsClient $client): mixed => $client->reportDefinitions()->update($account, $definition, $form)];
        yield 'reportDefinitions.delete' => [function (ReportsClient $client) use ($account, $definition): mixed {
            $client->reportDefinitions()->delete($account, $definition);

            return null;
        }];
        yield 'reportDefinitions.duplicate' => [fn (ReportsClient $client): mixed => $client->reportDefinitions()->duplicate($account, $definition)];
        yield 'reportDefinitions.resetToTemplate' => [fn (ReportsClient $client): mixed => $client->reportDefinitions()->resetToTemplate($account, $definition)];
        yield 'reportDefinitions.getData' => [fn (ReportsClient $client): mixed => $client->reportDefinitions()->getData($account, $definition)];
        yield 'reportDefinitions.listBlocks' => [fn (ReportsClient $client): mixed => $client->reportDefinitions()->listBlocks($account)];
        yield 'reportRuns.list' => [fn (ReportsClient $client): mixed => $client->reportRuns()->list($account, $definition)];
        yield 'reportRuns.create' => [fn (ReportsClient $client): mixed => $client->reportRuns()->create($account, $definition, CreateReportRunRequestDTO::fromArray(['grain' => Grain::MONTH, 'periodKey' => '2026-09']))];
        yield 'reportRuns.get' => [fn (ReportsClient $client): mixed => $client->reportRuns()->get($account, $definition, $run)];
        yield 'reportRuns.downloadPdf' => [fn (ReportsClient $client): mixed => $client->reportRuns()->downloadPdf($account, $definition, $run)];
        yield 'reportRuns.createShareLink' => [fn (ReportsClient $client): mixed => $client->reportRuns()->createShareLink($account, $definition, $period)];
        yield 'settings.get' => [fn (ReportsClient $client): mixed => $client->settings()->get($account)];
        yield 'settings.update' => [fn (ReportsClient $client): mixed => $client->settings()->update($account, UpdateReportSettingsDTO::empty())];
        $subscription = StubResponses::SUBSCRIPTION_ID;
        yield 'subscriptions.list' => [fn (ReportsClient $client): mixed => $client->subscriptions()->list($account)];
        yield 'subscriptions.create' => [fn (ReportsClient $client): mixed => $client->subscriptions()->create($account, SubscriptionFormDTO::empty())];
        yield 'subscriptions.get' => [fn (ReportsClient $client): mixed => $client->subscriptions()->get($account, $subscription)];
        yield 'subscriptions.update' => [fn (ReportsClient $client): mixed => $client->subscriptions()->update($account, $subscription, SubscriptionFormDTO::empty())];
        yield 'subscriptions.delete' => [function (ReportsClient $client) use ($account, $subscription): mixed {
            $client->subscriptions()->delete($account, $subscription);

            return null;
        }];
        yield 'subscriptions.sendNow' => [fn (ReportsClient $client): mixed => $client->subscriptions()->sendNow($account, $subscription)];
        yield 'subscriptions.listDeliveries' => [fn (ReportsClient $client): mixed => $client->subscriptions()->listDeliveries($account, $subscription)];
        yield 'apiClients.list' => [fn (ReportsClient $client): mixed => $client->apiClients()->list($account)];
        yield 'apiClients.create' => [fn (ReportsClient $client): mixed => $client->apiClients()->create($account, CreateApiClientRequestDTO::create('Script'))];
        yield 'apiClients.revoke' => [function (ReportsClient $client) use ($account): mixed {
            $client->apiClients()->revoke($account, StubResponses::API_CLIENT_ID);

            return null;
        }];
        yield 'operations.listTemplates' => [fn (ReportsClient $client): mixed => $client->operations()->listTemplates()];
        yield 'operations.queueBackfill' => [fn (ReportsClient $client): mixed => $client->operations()->queueBackfill($account, BackfillRequestDTO::empty())];
        yield 'operations.queueReaggregation' => [fn (ReportsClient $client): mixed => $client->operations()->queueReaggregation(ReaggregationRequestDTO::empty())];
    }

    /**
     * @param  callable(ReportsClient): mixed  $call
     */
    #[DataProvider('everyCall')]
    public function testTheFailStubThrowsFromEveryCall(callable $call): void
    {
        $this->expectException(ReportsServerException::class);
        $this->expectExceptionMessage(StubFailure::MESSAGE);
        $this->expectExceptionCode(StubFailure::STATUS_CODE);

        $call(new FailReportsStubClient());
    }

    private static function makeQuery(): QueryMetricsRequestDTO
    {
        return QueryMetricsRequestDTO::fromArray([
            'metricKeys' => ['orders.revenue'],
            'scope' => ScopeDTO::create(ScopeType::ACCOUNT, self::ACCOUNT),
            'period' => PeriodRequestDTO::month('2026-09'),
        ]);
    }
}
