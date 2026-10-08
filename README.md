# dotsplatform/reports-api-sdk

PHP SDK for the Dots **reports** service, REST API v1 (task §11.2 / §11.3).

## Install

```bash
composer require dotsplatform/reports-api-sdk
php artisan vendor:publish --tag=reports-api-sdk-config
```

`config/reports-server.php`:

| Key | Env | Meaning |
|---|---|---|
| `host` | `REPORTS_SERVER_HOST` | Base URL of the reports service, e.g. `https://reports.dotsplatform.com` |
| `token` | `INTERNAL_GATEWAY_TOKEN` | The shared secret of every Dots service |
| `connect_timeout` | – | Seconds to connect, 2 by default |
| `timeout` | – | Seconds for a whole JSON request, 8 by default |
| `pdf_timeout` | – | Seconds for a PDF download, 30 by default |

Every request sends `X-Internal-Gateway-Token: <token>`. The reports service answers **401** without it. See
`livesite/ai/2026-09/DEV-18603-microservices-auth-middleware/internal-request-authorization.md`.

Write calls take an optional `$actorId`, the livesite user who acts. It is sent as `X-Actor-Id`, and the service
stores it as `created_by` for the audit log.

## Usage

```php
use Dotsplatform\ReportsApi\ReportsClient;

$reportsClient = app(ReportsClient::class);
```

The client is split into groups:

| Group | Methods |
|---|---|
| `metrics()` | `listMetrics`, `listDimensions`, `listScopes` (cursor), `queryMetrics`, `getSnapshot` |
| `reportDefinitions()` | `list`, `create`, `get`, `update`, `delete`, `duplicate`, `resetToTemplate`, `getData`, `listBlocks` |
| `reportRuns()` | `list`, `create`, `get`, `downloadPdf` (bytes), `createShareLink` |
| `settings()` | `get`, `update` |
| `subscriptions()` | `list`, `create`, `get`, `update`, `delete`, `sendNow`, `listDeliveries` (latest 100, newest first) |
| `apiClients()` | `list`, `create` (the plain token is returned once), `revoke` |
| `operations()` | `listTemplates`, `queueBackfill`, `queueReaggregation` |

### Query metrics

```php
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\QueryMetricsRequestDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\SeriesRequestDTO;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ScopeType;

$result = $reportsClient->metrics()->queryMetrics($accountId, QueryMetricsRequestDTO::fromArray([
    'metricKeys' => ['orders.count', 'orders.revenue', 'orders.avg_check'],
    'scope' => ScopeDTO::create(ScopeType::ACCOUNT, $accountId),
    'series' => SeriesRequestDTO::last(6, Grain::MONTH),
]));

foreach ($result->getPeriods() as $period) {
    $revenue = $period->getMetric('orders.revenue')?->getMoney(); // MoneyDTO: getAmountCents(), getCurrency()
    $averageCheck = $period->getMetric('orders.avg_check')?->getRatio(); // RatioDTO: numerator, denominator, value
}
```

A period is chosen in exactly one way: `period` (one `PeriodRequestDTO`), `periods` (a list) or `series`.

### Values

- Ids are uuid strings.
- Money is a `MoneyDTO` (`amountCents`, `currency`). Amounts are never floats.
- Ratios and means are a `RatioDTO` (`numerator`, `denominator`, `value`).
- Periods are a `PeriodDTO` (`grain`, `key`, and `from` / `to` / `label` where the response has them).
- Durations are seconds; times are unix timestamps.

### Compatibility: enums in responses are tolerant

The service may add enum values (a new subscription format, delivery status, scope type, …) before every consumer
has upgraded the SDK. An unknown value must never break a whole response, so:

- every enum-backed field of a **response** DTO keeps the raw string: `getStatusValue(): string`;
- its typed getter returns the enum or **null** for a value this SDK version does not know: `getStatus(): ?DeliveryStatus`;
- code that branches on a value handles null (treat it as "unknown", show the raw value, or skip);
- helpers such as `ReportRunDTO::isPdfReady()` answer false for unknown values.

**Request** DTOs stay strict: they take enum cases, so the SDK never sends a value the service rejects. A field that
is not an enum but has an unexpected type (a string where an integer is documented) still throws
`ReportsUnexpectedResponseException`.

### Report definitions

```php
use Dotsplatform\ReportsApi\DTO\Reports\LayoutBlockDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDefinitionFormDTO;

$definition = $reportsClient->reportDefinitions()->create($accountId, ReportDefinitionFormDTO::fromArray([
    'templateKey' => 'weekly',
    'name' => 'Weekly for owners',
]), $actorId);

$reportsClient->reportDefinitions()->update($accountId, $definition->getId(), ReportDefinitionFormDTO::fromArray([
    'blocks' => [LayoutBlockDTO::create('header'), LayoutBlockDTO::create('kpi_group', ['group' => 'general'])],
]), $actorId);
```

Unset form fields are not sent, so `update` changes only what is set.

### Subscription channels

A subscription is delivered by email (the default) or Telegram. The channel is chosen on create and never changes:
`update()` does not send it. Every recipient must match the channel.

```php
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\RecipientRequestDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\SubscriptionFormDTO;
use Dotsplatform\ReportsApi\Enums\SubscriptionChannel;

$subscription = $reportsClient->subscriptions()->create($accountId, SubscriptionFormDTO::fromArray([
    'definitionId' => $definitionId,
    'channel' => SubscriptionChannel::TELEGRAM,
    'recipients' => [
        RecipientRequestDTO::telegramUser($livesiteUserId, 'Owner'), // a livesite user with Telegram connected
        RecipientRequestDTO::telegramChat($telegramChatId),          // a chat connected to the notifier bot
    ],
]), $actorId);
```

Email recipients use `RecipientRequestDTO::email($email, $name)`. In responses, a recipient has `getKind()`
(`RecipientKind`, tolerant like every response enum), a nullable `getEmail()`, `getTelegramChatId()` and
`getTelegramUserId()`.

### Clearing a field in an update

A `null` field in a form means "not given", so it is not sent. To **remove** a value, clear it explicitly. The
cleared fields are sent as JSON `null`:

```php
use Dotsplatform\ReportsApi\Enums\SubscriptionClearableField;

$form = SubscriptionFormDTO::fromArray(['sendTime' => '08:00'])
    ->clear(SubscriptionClearableField::LOCALE, SubscriptionClearableField::SEND_WEEKDAY);
// PATCH body: {"send_time": "08:00", "locale": null, "send_weekday": null}
```

- Subscriptions can clear `LOCALE`, `SCOPE_ID`, `SEND_WEEKDAY` and `SEND_MONTH_DAY`.
- Report definitions can clear `SCOPE_ID` (`ReportDefinitionClearableField`).
- `clear()` returns a copy. A cleared field is sent as null even when a value was set on the same form.

## Errors

Every call may throw a subclass of `Dotsplatform\ReportsApi\Exceptions\ReportsApiException`:

| Exception | When |
|---|---|
| `ReportsNotFoundException` | 404: unknown id, or an id of another account |
| `ReportsValidationException` | 422: `getErrors()` / `getErrorsFor('field')`, Laravel style |
| `ReportsUnauthorizedException` | 401: wrong or missing token |
| `ReportsForbiddenException` | 403: an account token without the ability |
| `ReportsRateLimitException` | 429: `getRetryAfterSeconds()` |
| `ReportsServerException` | any other error status |
| `ReportsConnectionException` | no response (DNS, timeout, refused) |
| `ReportsUnexpectedResponseException` | the body does not have the documented shape |
| `ReportsConfigurationException` | `reports-server.host` or `reports-server.token` is empty |

## Tests of consumers

```php
use Dotsplatform\ReportsApi\Clients\FailReportsStubClient;
use Dotsplatform\ReportsApi\Clients\SuccessReportsStubClient;
use Dotsplatform\ReportsApi\ReportsClient;

$this->app->bind(ReportsClient::class, SuccessReportsStubClient::class); // sample data, no HTTP
$this->app->bind(ReportsClient::class, FailReportsStubClient::class);    // every call throws ReportsServerException (503)
```

The sample data is in `Clients\Stubs\StubResponses`.

## Development

```bash
composer install
composer init-pre-commit   # fixer + phpstan + phpunit before each commit
composer test              # PHPUnit, HTTP mocked with Guzzle MockHandler
composer analyse           # PHPStan, level max
composer fix               # PHP CS Fixer (Laravel Shift style + strict rules)
```
