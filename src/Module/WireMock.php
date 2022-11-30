<?php
/**
 * This file is part of codeception-wiremock-extension.
 *
 * codeception-wiremock-extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * codeception-wiremock-extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with codeception-wiremock-extension.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Codeception\Module;

use Codeception\Module as CodeceptionModule;
use Codeception\Util\Debug;
use WireMock\Client\MappingBuilder;
use WireMock\Client\RequestPatternBuilder;
use WireMock\Client\WireMock as WireMockClient;

class WireMock extends CodeceptionModule
{
    private WireMockClient $wireMock;

    protected array $config = [
        'host' => 'localhost',
        'port' => '8080',
    ];

    /**
     * {@inheritDoc}
     */
    public function _beforeSuite($settings = []): void
    {
        $this->config = array_merge($this->config, $settings);
        Debug::debug("Connecting to WireMock in: host {$this->config['host']} and port {$this->config['port']}");
        $this->wireMock = WireMockClient::create($this->config['host'], $this->config['port']);
    }

    public function expectRequestToWireMock(MappingBuilder $builder): void
    {
        $this->wireMock->stubFor($builder);
    }

    public function receivedRequestToWireMock(RequestPatternBuilder|int $builderOrCount, RequestPatternBuilder $builder = null): void
    {
        $this->wireMock->verify($builderOrCount, $builder);
    }

    /**
     * @return \WireMock\Client\LoggedRequest[]
     */
    public function findReceivedRequestsToWireMock(RequestPatternBuilder $builder): array
    {
        return $this->wireMock->findAll($builder);
    }

    public function resetMappingsAndRequestJournalInWireMock(): void
    {
        $this->wireMock->reset();
    }

    public function resetRequestJournalInWireMock(): void
    {
        $this->wireMock->resetAllRequests();
    }

    public function resetMappingsInWireMock(): void
    {
        $this->wireMock->resetToDefault();
    }
}
