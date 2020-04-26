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

namespace Ekino\Drupal\Debug\Action\EnableDebugCacheabilityHeader;

use Ekino\Drupal\Debug\Action\AbstractOverrideBoolParameterAction;

class EnableDebugCacheabilityHeaderAction extends AbstractOverrideBoolParameterAction
{
    /**
     * {@inheritdoc}
     */
    protected static function getParameterId(): string
    {
        return 'http.response.debug_cacheability_headers';
    }

    /**
     * {@inheritdoc}
     */
    protected function getOverride(): bool
    {
        return true;
    }
}
