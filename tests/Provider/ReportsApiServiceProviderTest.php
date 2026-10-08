<?php

declare(strict_types=1);

/**
 * Description of ReportsApiServiceProviderTest.php
 *
 * The service provider in a Laravel app (Testbench): config merged under reports-server, ReportsClient bound to the
 * HTTP client, the transport built from the config, and the config file published.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Provider;

use Dotsplatform\ReportsApi\Clients\ReportsHttpClient;
use Dotsplatform\ReportsApi\Exceptions\ReportsConfigurationException;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\ReportsApiServiceProvider;
use Dotsplatform\ReportsApi\ReportsClient;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase;

class ReportsApiServiceProviderTest extends TestCase
{
    public function testTheConfigIsMergedUnderReportsServer(): void
    {
        $this->assertSame('https://reports.example.test', config('reports-server.host'));
        $this->assertSame('internal-gateway-secret', config('reports-server.token'));
        $this->assertSame(2, config('reports-server.connect_timeout'));
        $this->assertSame(8, config('reports-server.timeout'));
        $this->assertSame(30, config('reports-server.pdf_timeout'));
    }

    public function testTheClientIsTheHttpClient(): void
    {
        $this->assertInstanceOf(ReportsHttpClient::class, $this->app?->make(ReportsClient::class));
    }

    public function testAnEmptyTokenFailsLoudly(): void
    {
        config(['reports-server.token' => '']);
        $this->app?->forgetInstance(ReportsHttpTransport::class);

        $this->expectException(ReportsConfigurationException::class);

        $reportsClient = $this->app?->make(ReportsClient::class);
        $this->assertInstanceOf(ReportsClient::class, $reportsClient);
        $reportsClient->operations()->listTemplates();
    }

    public function testTheConfigFileIsPublished(): void
    {
        $publishedPaths = ServiceProvider::pathsToPublish(ReportsApiServiceProvider::class, 'reports-api-sdk-config');

        $this->assertCount(1, $publishedPaths);
        $publishedPath = array_values($publishedPaths)[0];
        $this->assertIsString($publishedPath);
        $this->assertStringEndsWith('config/reports-server.php', $publishedPath);
    }

    /**
     * @param  Application  $app
     * @return list<class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [ReportsApiServiceProvider::class];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $config = $app->make(ConfigRepository::class);
        $config->set('reports-server.host', 'https://reports.example.test');
        $config->set('reports-server.token', 'internal-gateway-secret');
    }
}
