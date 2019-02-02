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

namespace Ekino\Drupal\Debug\Action\WatchModulesHooksImplementations;

use Drupal\Core\DrupalKernelInterface;
use Drupal\Core\Extension\ModuleHandler;
use Ekino\Drupal\Debug\Cache\Event\CacheNotFreshEvent;
use Ekino\Drupal\Debug\Extension\Model\CustomModule;
use Ekino\Drupal\Debug\Resource\Model\CustomExtensionFileResource;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LoadNewModuleFile
{
    /**
     * @var ModuleHandler
     */
    private $moduleHandler;

    /**
     * @var HttpKernelInterface
     */
    private $kernel;

    public function __construct(ModuleHandler $moduleHandler, HttpKernelInterface $kernel)
    {
        $this->moduleHandler = $moduleHandler;
        $this->kernel = $kernel;
    }

    public function __invoke(CacheNotFreshEvent $event): void
    {
        foreach ($event->getFileCache()->getCurrentResourcesCollection()->all() as $resource) {
            if (!$resource instanceof CustomExtensionFileResource) {
                continue;
            }

            if (!$resource->isNew()) {
                continue;
            }

            $customExtension = $resource->getCustomExtension();
            if (!$customExtension instanceof CustomModule) {
                continue;
            }

            $machineName = $customExtension->getMachineName();
            $extensionFilename = \sprintf('%s.module', $machineName);
            if ($extensionFilename !== \substr($resource->getFilePath(), -\strlen($extensionFilename))) {
                continue;
            }

            $module = $this->moduleHandler->getModule($machineName);
            if (\is_string($module->getExtensionFilename())) {
                continue;
            }

            $name = $module->getName();

            $this->moduleHandler->addModule($name, $module->getPath());
            $this->moduleHandler->getModule($name)->load();

            if ($this->kernel instanceof DrupalKernelInterface) {
                $this->kernel->invalidateContainer();
            }
        }
    }
}
