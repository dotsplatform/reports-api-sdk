<?php

declare(strict_types=1);

/**
 * Description of SubscriptionsHttpClientTest.php
 *
 * subscriptions(): create with recipients and the actor, PATCH with only the set fields, list, show, delete,
 * send-now and the delivery log.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\RecipientRequestDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\SubscriptionFormDTO;
use Dotsplatform\ReportsApi\Enums\DeliveryStatus;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\RecipientKind;
use Dotsplatform\ReportsApi\Enums\RecipientStatus;
use Dotsplatform\ReportsApi\Enums\ReportLocale;
use Dotsplatform\ReportsApi\Enums\ScopeType;
use Dotsplatform\ReportsApi\Enums\SubscriptionChannel;
use Dotsplatform\ReportsApi\Enums\SubscriptionClearableField;
use Dotsplatform\ReportsApi\Enums\SubscriptionFormat;
use Dotsplatform\ReportsApi\Http\RequestHeader;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;

class SubscriptionsHttpClientTest extends HttpClientTestCase
{
    private const string ACCOUNT = StubResponses::ACCOUNT_ID;

    private const string SUBSCRIPTION = StubResponses::SUBSCRIPTION_ID;

    private const string SUBSCRIPTIONS_PATH = '/api/v1/accounts/'.self::ACCOUNT.'/subscriptions';

    public function testCreatesASubscriptionWithRecipients(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::subscription()], 201);

        $subscription = $this->makeClient()->subscriptions()->create(self::ACCOUNT, SubscriptionFormDTO::fromArray([
            'definitionId' => StubResponses::DEFINITION_ID,
            'scopeType' => ScopeType::ACCOUNT,
            'grain' => Grain::ISO_WEEK,
            'sendTime' => '09:00',
            'sendWeekday' => 1,
            'format' => SubscriptionFormat::DIGEST,
            'locale' => ReportLocale::EN,
            'isActive' => true,
            'recipients' => [RecipientRequestDTO::create('owner@example.test', 'Owner'), RecipientRequestDTO::create('manager@example.test')],
        ]), self::ACTOR_ID);

        $this->assertLastRequest('POST', self::SUBSCRIPTIONS_PATH);
        $this->assertSame(self::ACTOR_ID, $this->getLastRequest()->getHeaderLine(RequestHeader::ACTOR_ID));
        $this->assertSame([
            'definition_id' => StubResponses::DEFINITION_ID,
            'scope_type' => 'account',
            'grain' => 'iso_week',
            'send_time' => '09:00',
            'send_weekday' => 1,
            'format' => 'digest',
            'locale' => 'en',
            'is_active' => true,
            'recipients' => [
                ['email' => 'owner@example.test', 'name' => 'Owner'],
                ['email' => 'manager@example.test', 'name' => null],
            ],
        ], $this->getLastRequestJson());

        $this->assertSame(self::SUBSCRIPTION, $subscription->getId());
        $this->assertSame(StubResponses::DEFINITION_ID, $subscription->getDefinitionId());
        $this->assertSame(ScopeType::ACCOUNT, $subscription->getScope()->getScopeType());
        $this->assertSame(Grain::ISO_WEEK, $subscription->getGrain());
        $this->assertSame('09:00', $subscription->getSchedule()->getSendTime());
        $this->assertSame(1, $subscription->getSchedule()->getSendWeekday());
        $this->assertNull($subscription->getSchedule()->getSendMonthDay());
        $this->assertSame(SubscriptionChannel::EMAIL, $subscription->getChannel());
        $this->assertSame('email', $subscription->getChannelValue());
        $this->assertSame(SubscriptionFormat::DIGEST, $subscription->getFormat());
        $this->assertNull($subscription->getLocale());
        $this->assertTrue($subscription->isActive());
        $this->assertNull($subscription->getPausedReason());
        $this->assertSame(StubResponses::CREATED_AT + 86400, $subscription->getNextDueAt());
        $this->assertNull($subscription->getLastSentAt());
        $this->assertNull($subscription->getLastPeriodKey());
        $this->assertSame('livesite-user-7', $subscription->getCreatedBy());
        $recipient = $subscription->getRecipients()[0];
        $this->assertSame(StubResponses::RECIPIENT_ID, $recipient->getId());
        $this->assertSame(RecipientKind::EMAIL, $recipient->getKind());
        $this->assertSame('owner@example.test', $recipient->getEmail());
        $this->assertNull($recipient->getTelegramChatId());
        $this->assertNull($recipient->getTelegramUserId());
        $this->assertSame('Owner', $recipient->getName());
        $this->assertSame(RecipientStatus::ACTIVE, $recipient->getStatus());
        $this->assertTrue($recipient->isActive());
        $this->assertNull($recipient->getUnsubscribedAt());
    }

    public function testCreatesATelegramSubscriptionWithUserAndChatRecipients(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::telegramSubscription()], 201);

        $subscription = $this->makeClient()->subscriptions()->create(self::ACCOUNT, SubscriptionFormDTO::fromArray([
            'definitionId' => StubResponses::DEFINITION_ID,
            'channel' => SubscriptionChannel::TELEGRAM,
            'recipients' => [
                RecipientRequestDTO::telegramUser(StubResponses::TELEGRAM_USER_ID, 'Owner'),
                RecipientRequestDTO::telegramChat(StubResponses::TELEGRAM_CHAT_ID),
            ],
        ]));

        $this->assertLastRequest('POST', self::SUBSCRIPTIONS_PATH);
        $this->assertSame([
            'definition_id' => StubResponses::DEFINITION_ID,
            'recipients' => [
                ['telegram_user_id' => StubResponses::TELEGRAM_USER_ID, 'name' => 'Owner'],
                ['telegram_chat_id' => StubResponses::TELEGRAM_CHAT_ID, 'name' => null],
            ],
            'channel' => 'telegram',
        ], $this->getLastRequestJson());

        $this->assertSame(SubscriptionChannel::TELEGRAM, $subscription->getChannel());
        $recipient = $subscription->getRecipients()[0];
        $this->assertSame(StubResponses::TELEGRAM_RECIPIENT_ID, $recipient->getId());
        $this->assertSame(RecipientKind::TELEGRAM, $recipient->getKind());
        $this->assertSame('telegram', $recipient->getKindValue());
        $this->assertTrue($recipient->isTelegram());
        $this->assertNull($recipient->getEmail());
        $this->assertSame(StubResponses::TELEGRAM_CHAT_ID, $recipient->getTelegramChatId());
        $this->assertSame(StubResponses::TELEGRAM_USER_ID, $recipient->getTelegramUserId());
    }

    public function testUpdateNeverSendsTheChannel(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::telegramSubscription()]);
        $form = SubscriptionFormDTO::fromArray([
            'channel' => SubscriptionChannel::TELEGRAM,
            'recipients' => [RecipientRequestDTO::telegramUser(StubResponses::TELEGRAM_USER_ID)],
        ]);

        $this->makeClient()->subscriptions()->update(self::ACCOUNT, StubResponses::TELEGRAM_SUBSCRIPTION_ID, $form);

        $this->assertLastRequest('PATCH', self::SUBSCRIPTIONS_PATH.'/'.StubResponses::TELEGRAM_SUBSCRIPTION_ID);
        $this->assertSame([
            'recipients' => [['telegram_user_id' => StubResponses::TELEGRAM_USER_ID, 'name' => null]],
        ], $this->getLastRequestJson());
        $this->assertArrayNotHasKey('channel', $form->toUpdateRequestBody());
        $this->assertSame('telegram', $form->toRequestBody()['channel']);
    }

    public function testUpdateSendsOnlyTheSetFields(): void
    {
        $paused = StubResponses::subscription();
        $paused['is_active'] = false;
        $paused['paused_reason'] = 'all_recipients_suppressed';
        $paused['recipients'] = [
            ['id' => StubResponses::RECIPIENT_ID, 'email' => 'owner@example.test', 'name' => null, 'status' => 'suppressed', 'unsubscribed_at' => null],
        ];
        $this->queueJsonResponse(['data' => $paused]);

        $subscription = $this->makeClient()->subscriptions()->update(self::ACCOUNT, self::SUBSCRIPTION, SubscriptionFormDTO::fromArray([
            'isActive' => false,
            'sendMonthDay' => 3,
        ]));

        $this->assertLastRequest('PATCH', self::SUBSCRIPTIONS_PATH.'/'.self::SUBSCRIPTION);
        $this->assertSame(['send_month_day' => 3, 'is_active' => false], $this->getLastRequestJson());
        $this->assertFalse($subscription->isActive());
        $this->assertSame('all_recipients_suppressed', $subscription->getPausedReason());
        $this->assertFalse($subscription->getRecipients()[0]->isActive());
    }

    public function testClearedFieldsAreSentAsJsonNull(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::subscription()]);
        $form = SubscriptionFormDTO::fromArray(['grain' => Grain::DAY])
            ->clear(SubscriptionClearableField::LOCALE, SubscriptionClearableField::SEND_WEEKDAY)
            ->clear(SubscriptionClearableField::SCOPE_ID, SubscriptionClearableField::SEND_MONTH_DAY, SubscriptionClearableField::LOCALE);

        $this->makeClient()->subscriptions()->update(self::ACCOUNT, self::SUBSCRIPTION, $form);

        $this->assertSame([
            'grain' => 'day',
            'locale' => null,
            'send_weekday' => null,
            'scope_id' => null,
            'send_month_day' => null,
        ], $this->getLastRequestJson());
        $this->assertSame('{"grain":"day","locale":null,"send_weekday":null,"scope_id":null,"send_month_day":null}', $this->getLastRequestRawBody());
    }

    public function testFieldsNeverSetAreNotSentAndClearingDoesNotChangeTheOriginal(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::subscription()]);
        $form = SubscriptionFormDTO::fromArray(['locale' => ReportLocale::UA, 'sendWeekday' => 3]);

        $clearedForm = $form->clear(SubscriptionClearableField::LOCALE);

        $this->assertSame(['send_weekday' => 3, 'locale' => 'ua'], $form->toRequestBody());
        $this->assertSame([], $form->getClearedFields());
        $this->assertSame([SubscriptionClearableField::LOCALE], $clearedForm->getClearedFields());

        // A cleared field wins over a value set on the same form.
        $this->makeClient()->subscriptions()->update(self::ACCOUNT, self::SUBSCRIPTION, $clearedForm);
        $this->assertSame(['send_weekday' => 3, 'locale' => null], $this->getLastRequestJson());
    }

    public function testListsShowsAndDeletes(): void
    {
        $this->queueJsonResponse(['data' => [StubResponses::subscription()]]);
        $this->queueJsonResponse(['data' => StubResponses::subscription()]);
        $this->queueNoContentResponse();
        $subscriptions = $this->makeClient()->subscriptions();

        $this->assertCount(1, $subscriptions->list(self::ACCOUNT));
        $this->assertLastRequest('GET', self::SUBSCRIPTIONS_PATH);

        $this->assertSame(self::SUBSCRIPTION, $subscriptions->get(self::ACCOUNT, self::SUBSCRIPTION)->getId());
        $this->assertLastRequest('GET', self::SUBSCRIPTIONS_PATH.'/'.self::SUBSCRIPTION);

        $subscriptions->delete(self::ACCOUNT, self::SUBSCRIPTION, self::ACTOR_ID);
        $this->assertLastRequest('DELETE', self::SUBSCRIPTIONS_PATH.'/'.self::SUBSCRIPTION);
        $this->assertSame(self::ACTOR_ID, $this->getLastRequest()->getHeaderLine(RequestHeader::ACTOR_ID));
    }

    public function testSendsNow(): void
    {
        $this->queueJsonResponse(['data' => ['status' => 'queued']], 202);

        $status = $this->makeClient()->subscriptions()->sendNow(self::ACCOUNT, self::SUBSCRIPTION, self::ACTOR_ID);

        $this->assertLastRequest('POST', self::SUBSCRIPTIONS_PATH.'/'.self::SUBSCRIPTION.'/send-now');
        $this->assertSame('{}', $this->getLastRequestRawBody());
        $this->assertSame('queued', $status);
    }

    public function testListsDeliveries(): void
    {
        $failed = StubResponses::delivery();
        $failed['status'] = 'bounced';
        $failed['error'] = 'Permanent bounce';
        $failed['recipient_email'] = null;
        $failed['delivered_at'] = null;
        $this->queueJsonResponse(['data' => [StubResponses::delivery(), $failed]]);

        $deliveries = $this->makeClient()->subscriptions()->listDeliveries(self::ACCOUNT, self::SUBSCRIPTION);

        $this->assertLastRequest('GET', self::SUBSCRIPTIONS_PATH.'/'.self::SUBSCRIPTION.'/deliveries');
        $this->assertCount(2, $deliveries);
        $delivered = $deliveries[0];
        $this->assertSame(StubResponses::DELIVERY_ID, $delivered->getId());
        $this->assertSame(StubResponses::RECIPIENT_ID, $delivered->getRecipientId());
        $this->assertSame('owner@example.test', $delivered->getRecipientEmail());
        $this->assertSame(self::SUBSCRIPTION.':2026-W41', $delivered->getSendKey());
        $this->assertSame(StubResponses::RUN_ID, $delivered->getRunId());
        $this->assertSame(SubscriptionChannel::EMAIL, $delivered->getChannel());
        $this->assertSame(DeliveryStatus::DELIVERED, $delivered->getStatus());
        $this->assertNull($delivered->getError());
        $this->assertSame(StubResponses::CREATED_AT, $delivered->getSentAt());
        $this->assertSame(StubResponses::CREATED_AT + 5, $delivered->getDeliveredAt());
        $this->assertSame(StubResponses::CREATED_AT, $delivered->getCreatedAt());
        $this->assertSame(DeliveryStatus::BOUNCED, $deliveries[1]->getStatus());
        $this->assertSame('Permanent bounce', $deliveries[1]->getError());
        $this->assertNull($deliveries[1]->getRecipientEmail());
        $this->assertNull($deliveries[1]->getDeliveredAt());
    }
}
