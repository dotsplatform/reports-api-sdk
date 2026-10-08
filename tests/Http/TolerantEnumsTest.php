<?php

declare(strict_types=1);

/**
 * Description of TolerantEnumsTest.php
 *
 * E-S11: an enum value this SDK version does not know does not fail the response. The DTO keeps the raw string
 * (get…Value()) and the typed getter returns null; known values still map to the enum.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Http;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\ReportRunStatus;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;

class TolerantEnumsTest extends HttpClientTestCase
{
    public function testUnknownSubscriptionValuesAreKeptRaw(): void
    {
        $subscriptionData = StubResponses::subscription();
        $subscriptionData['grain'] = 'quarter';
        $subscriptionData['channel'] = 'telegram';
        $subscriptionData['format'] = 'digest_with_csv';
        $subscriptionData['scope'] = ['type' => 'courier_fleet', 'id' => StubResponses::CITY_ID];
        $subscriptionData['recipients'] = [
            ['id' => StubResponses::RECIPIENT_ID, 'email' => 'owner@example.test', 'name' => null, 'status' => 'snoozed', 'unsubscribed_at' => null],
        ];
        $this->queueJsonResponse(['data' => $subscriptionData]);

        $subscription = $this->makeClient()->subscriptions()->get(StubResponses::ACCOUNT_ID, StubResponses::SUBSCRIPTION_ID);

        $this->assertNull($subscription->getGrain());
        $this->assertSame('quarter', $subscription->getGrainValue());
        $this->assertNull($subscription->getChannel());
        $this->assertSame('telegram', $subscription->getChannelValue());
        $this->assertNull($subscription->getFormat());
        $this->assertSame('digest_with_csv', $subscription->getFormatValue());
        $this->assertNull($subscription->getScope()->getScopeType());
        $this->assertSame('courier_fleet', $subscription->getScope()->getScopeTypeValue());
        $recipient = $subscription->getRecipients()[0];
        $this->assertNull($recipient->getStatus());
        $this->assertSame('snoozed', $recipient->getStatusValue());
        $this->assertFalse($recipient->isActive());
    }

    public function testAnUnknownDeliveryStatusIsKeptRaw(): void
    {
        $deliveryData = StubResponses::delivery();
        $deliveryData['status'] = 'opened';
        $deliveryData['channel'] = 'telegram';
        $this->queueJsonResponse(['data' => [$deliveryData]]);

        $delivery = $this->makeClient()->subscriptions()->listDeliveries(StubResponses::ACCOUNT_ID, StubResponses::SUBSCRIPTION_ID)[0];

        $this->assertNull($delivery->getStatus());
        $this->assertSame('opened', $delivery->getStatusValue());
        $this->assertNull($delivery->getChannel());
        $this->assertSame('telegram', $delivery->getChannelValue());
    }

    public function testAnUnknownRunStatusIsKeptRaw(): void
    {
        $runData = StubResponses::reportRun(StubResponses::RUN_ID, 'pdf_archived');
        $runData['compare'] = 'same_weekday_last_week';
        $this->queueJsonResponse(['data' => $runData]);

        $run = $this->makeClient()->reportRuns()->get(StubResponses::ACCOUNT_ID, StubResponses::DEFINITION_ID, StubResponses::RUN_ID);

        $this->assertNull($run->getStatus());
        $this->assertSame('pdf_archived', $run->getStatusValue());
        $this->assertFalse($run->isPdfReady());
        $this->assertNull($run->getCompare());
        $this->assertSame('same_weekday_last_week', $run->getCompareValue());
    }

    public function testKnownValuesStillMapAndANullStaysNull(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::reportRun(StubResponses::RUN_ID, 'pdf_ready')]);

        $run = $this->makeClient()->reportRuns()->get(StubResponses::ACCOUNT_ID, StubResponses::DEFINITION_ID, StubResponses::RUN_ID);

        $this->assertSame(ReportRunStatus::PDF_READY, $run->getStatus());
        $this->assertSame('pdf_ready', $run->getStatusValue());
        $this->assertTrue($run->isPdfReady());
        $this->assertNull($run->getCompare());
        $this->assertNull($run->getCompareValue());
    }

    public function testUnknownDefinitionAndSettingsValuesAreKeptRaw(): void
    {
        $definitionData = StubResponses::reportDefinition();
        $definitionData['purpose'] = 'billboard';
        $definitionData['default_grain'] = 'quarter';
        $definitionData['comparison'] = CompareMode::NONE->value;
        $settingsData = StubResponses::reportSettings();
        $settingsData['currency_position'] = 'middle';
        $settingsData['plan_profile'] = 'enterprise';
        $this->queueJsonResponse(['data' => $definitionData]);
        $this->queueJsonResponse(['data' => $settingsData]);

        $definition = $this->makeClient()->reportDefinitions()->get(StubResponses::ACCOUNT_ID, StubResponses::DEFINITION_ID);
        $this->assertNull($definition->getPurpose());
        $this->assertSame('billboard', $definition->getPurposeValue());
        $this->assertNull($definition->getDefaultGrain());
        $this->assertSame('quarter', $definition->getDefaultGrainValue());
        $this->assertSame(CompareMode::NONE, $definition->getComparison());

        $settings = $this->makeClient()->settings()->get(StubResponses::ACCOUNT_ID);
        $this->assertNull($settings->getCurrencyPosition());
        $this->assertSame('middle', $settings->getCurrencyPositionValue());
        $this->assertNull($settings->getPlanProfile());
        $this->assertSame('enterprise', $settings->getPlanProfileValue());
    }

    public function testAnUnknownScopeTypeInTheScopeListIsKeptRaw(): void
    {
        $this->queueJsonResponse(['data' => [['type' => 'courier_fleet', 'id' => StubResponses::CITY_ID, 'name' => 'Fleet', 'parent_id' => null]], 'next_cursor' => null]);

        $scope = $this->makeClient()->metrics()->listScopes(StubResponses::ACCOUNT_ID)->getScopes()[0];

        $this->assertNull($scope->getScopeType());
        $this->assertSame('courier_fleet', $scope->getScopeTypeValue());
        $this->assertSame('courier_fleet', $scope->toScope()->toRequestBody()['type']);
    }
}
