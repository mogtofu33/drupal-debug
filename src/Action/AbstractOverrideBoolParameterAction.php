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

namespace Ekino\Drupal\Debug\Action;

use Ekino\Drupal\Debug\Exception\NotSupportedException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractOverrideBoolParameterAction implements CompilerPassActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter(static::getParameterId())) {
            throw new NotSupportedException(\sprintf('The "%s" parameter should already be set in the container builder.', static::getParameterId()));
        }

        $container->setParameter(static::getParameterId(), $this->getOverride());
    }

    /**
     * @return string
     */
    abstract protected static function getParameterId(): string;

    /**
     * @return bool
     */
    abstract protected function getOverride(): bool;
}
