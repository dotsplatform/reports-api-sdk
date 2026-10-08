<?php

declare(strict_types=1);

/**
 * Description of ApiClientsHttpClientTest.php
 *
 * apiClients(): list, issue (the plain token once) and revoke.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\DTO\ApiClients\Requests\CreateApiClientRequestDTO;
use Dotsplatform\ReportsApi\Enums\ApiAbility;
use Dotsplatform\ReportsApi\Http\RequestHeader;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;

class ApiClientsHttpClientTest extends HttpClientTestCase
{
    private const string ACCOUNT = StubResponses::ACCOUNT_ID;

    private const string API_CLIENTS_PATH = '/api/v1/accounts/'.self::ACCOUNT.'/api-clients';

    public function testIssuesATokenOnce(): void
    {
        $this->queueJsonResponse(StubResponses::issuedApiClient('Claude'), 201);

        $issued = $this->makeClient()->apiClients()->create(
            self::ACCOUNT,
            CreateApiClientRequestDTO::create('Claude', [ApiAbility::METRICS_READ, ApiAbility::REPORTS_WRITE]),
            self::ACTOR_ID,
        );

        $this->assertLastRequest('POST', self::API_CLIENTS_PATH);
        $this->assertSame(self::ACTOR_ID, $this->getLastRequest()->getHeaderLine(RequestHeader::ACTOR_ID));
        $this->assertSame(['name' => 'Claude', 'abilities' => ['metrics:read', 'reports:write']], $this->getLastRequestJson());
        $this->assertSame('1|dotsr_stubplaintoken', $issued->getPlainToken());
        $this->assertSame(StubResponses::API_CLIENT_ID, $issued->getApiClient()->getId());
        $this->assertSame('Claude', $issued->getApiClient()->getName());
        $this->assertSame(['metrics:read', 'reports:read'], $issued->getApiClient()->getAbilities());
        $this->assertSame('livesite-user-7', $issued->getApiClient()->getCreatedBy());
        $this->assertSame(StubResponses::CREATED_AT, $issued->getApiClient()->getCreatedAt());
        $this->assertNull($issued->getApiClient()->getLastUsedAt());
    }

    public function testWithoutAbilitiesTheServiceDefaultIsUsed(): void
    {
        $this->queueJsonResponse(StubResponses::issuedApiClient('Script'), 201);

        $this->makeClient()->apiClients()->create(self::ACCOUNT, CreateApiClientRequestDTO::create('Script'));

        $this->assertSame(['name' => 'Script'], $this->getLastRequestJson());
    }

    public function testListsAndRevokes(): void
    {
        $usedClient = StubResponses::apiClient();
        $usedClient['last_used_at'] = StubResponses::CREATED_AT + 60;
        $this->queueJsonResponse(['data' => [$usedClient]]);
        $this->queueNoContentResponse();

        $apiClients = $this->makeClient()->apiClients()->list(self::ACCOUNT);
        $this->assertLastRequest('GET', self::API_CLIENTS_PATH);
        $this->assertSame(StubResponses::CREATED_AT + 60, $apiClients[0]->getLastUsedAt());

        $this->makeClient()->apiClients()->revoke(self::ACCOUNT, StubResponses::API_CLIENT_ID, self::ACTOR_ID);
        $this->assertLastRequest('DELETE', self::API_CLIENTS_PATH.'/'.StubResponses::API_CLIENT_ID);
    }
}
