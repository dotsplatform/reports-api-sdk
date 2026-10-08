<?php

declare(strict_types=1);

/**
 * Description of ReportsApiServiceProvider.php
 *
 * Binds ReportsClient to the HTTP client and merges / publishes config/reports-server.php
 * (php artisan vendor:publish --tag=reports-api-sdk-config).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi;

use Dotsplatform\ReportsApi\Clients\ReportsHttpClient;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\ServiceProvider;

class ReportsApiServiceProvider extends ServiceProvider
{
    public const string CONFIG_KEY = 'reports-server';

    private const string CONFIG_FILE = __DIR__.'/../config/reports-server.php';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_FILE, self::CONFIG_KEY);

        $this->app->singleton(ReportsHttpTransport::class, fn (): ReportsHttpTransport => $this->makeTransport());
        $this->app->bind(ReportsClient::class, ReportsHttpClient::class);
    }

    public function boot(): void
    {
        $this->publishes([
            self::CONFIG_FILE => $this->app->configPath('reports-server.php'),
        ], 'reports-api-sdk-config');
    }

    private function makeTransport(): ReportsHttpTransport
    {
        $config = $this->getConfigRepository();

        return new ReportsHttpTransport(
            $this->readString($config, 'host'),
            $this->readString($config, 'token'),
            $this->readTimeoutSeconds($config),
        );
    }

    private function getConfigRepository(): ConfigRepository
    {
        return $this->app->make(ConfigRepository::class);
    }

    private function readString(ConfigRepository $config, string $key): string
    {
        $value = $config->get(self::CONFIG_KEY.'.'.$key);
        if (! is_string($value)) {
            return '';
        }

        return $value;
    }

    private function readTimeoutSeconds(ConfigRepository $config): float
    {
        $value = $config->get(self::CONFIG_KEY.'.timeout_seconds');
        if (! is_numeric($value)) {
            return 30.0;
        }

        return (float) $value;
    }
}
