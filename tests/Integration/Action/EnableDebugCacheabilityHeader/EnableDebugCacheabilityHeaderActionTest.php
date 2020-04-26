<?php

declare(strict_types=1);

/*
 * This file is part of the ekino Drupal Debug project.
 *
 * (c) ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Drupal\Debug\Tests\Integration\Action\EnableDebugCacheabilityHeader;

use Ekino\Drupal\Debug\Tests\Integration\Action\AbstractActionTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Response;

class EnableDebugCacheabilityHeaderActionTest extends AbstractActionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function doTestInitialBehaviorWithDrupalKernel(Client $client): void
    {
        $headers = \array_keys($this->executeScenario($client));
        $this->assertNotContains(array('X-Drupal-Cache-Tags', 'X-Drupal-Cache-Contexts'), $headers);
    }

    /**
     * {@inheritdoc}
     */
    protected function doTestTargetedBehaviorWithDebugKernel(Client $client): void
    {
        $headers = $this->executeScenario($client);
        $this->assertArrayHasKey('X-Drupal-Cache-Tags', $headers);
        $this->assertArrayHasKey('X-Drupal-Cache-Contexts', $headers);
    }

    private function executeScenario(Client $client): array
    {
        $client->request('GET', '/');

        /** @var Response $response */
        $response = $client->getResponse();

        return $response->getHeaders();
    }
}
