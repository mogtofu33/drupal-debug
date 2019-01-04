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

namespace Ekino\Drupal\Debug\Cache;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Ekino\Drupal\Debug\Cache\Event\CacheNotFreshEvent;
use Ekino\Drupal\Debug\Cache\Event\FileBackendEvents;
use Ekino\Drupal\Debug\Exception\NotImplementedException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FileBackend implements CacheBackendInterface
{
    /**
     * @var FileCache
     */
    private $fileCache;

    /**
     * @var EventDispatcherInterface|null
     */
    private $eventDispatcher;

    /**
     * @param FileCache $fileCache
     */
    public function __construct(FileCache $fileCache)
    {
        $this->fileCache = $fileCache;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cid, $allow_invalid = false)
    {
        if ($allow_invalid) {
            throw new NotImplementedException('$allow_invalid with true value is not implemented.');
        }

        if (!$this->fileCache->isFresh()) {
            $this->dispatch(FileBackendEvents::ON_CACHE_NOT_FRESH, new CacheNotFreshEvent($this->fileCache));

            return false;
        }

        $data = $this->fileCache->getData();
        if (!\array_key_exists($cid, $data)) {
            return false;
        }

        $object = new \stdClass();
        $object->data = $data[$cid];

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple(&$cids, $allow_invalid = false): array
    {
        throw new NotImplementedException('The getMultiple() method is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function set($cid, $data, $expire = Cache::PERMANENT, array $tags = array()): void
    {
        if (Cache::PERMANENT !== $expire) {
            throw new NotImplementedException(\sprintf('$expire argument with "%s" value is not implemented.', $expire));
        }

        if (!empty($tags)) {
            throw new NotImplementedException('Non empty $tags argument is not implemented.');
        }

        $this->fileCache->write(array(
            $cid => $data,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple(array $items): void
    {
        throw new NotImplementedException('The setMultiple() method is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($cid): void
    {
        $data = $this->fileCache->get();
        if (!\is_array($data)) {
            return;
        }

        unset($data['data'][$cid]);

        $this->fileCache->write($data['data']);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple(array $cids): void
    {
        throw new NotImplementedException('The deleteMultiple() method is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll(): void
    {
        $this->fileCache->invalidate();
    }

    /**
     * {@inheritdoc}
     */
    public function invalidate($cid): void
    {
        throw new NotImplementedException('The invalidate() method is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateMultiple(array $cids): void
    {
        throw new NotImplementedException('The invalidateMultiple() method is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateAll(): void
    {
        throw new NotImplementedException('The invalidateAll() method is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function garbageCollection(): void
    {
        throw new NotImplementedException('The garbageCollection() method is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function removeBin(): void
    {
        throw new NotImplementedException('The removeBin() method is not implemented.');
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string     $eventName
     * @param Event|null $event
     */
    private function dispatch(string $eventName, ?Event $event): void
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($eventName, $event);
        }
    }
}
