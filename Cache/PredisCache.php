<?php
/*
 * This file is part of the StfalconStudioDoctrineRedisCacheBundle.
 *
 * (c) Stfalcon LLC <stfalcon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace StfalconStudio\DoctrineRedisCacheBundle\Cache;

use Doctrine\Common\Cache\PredisCache as BasePredisCache;
use Predis\ClientInterface;
use StfalconStudio\DoctrineRedisCacheBundle\Service\Migration\MigrationVersionService;

/**
 * PredisCache.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class PredisCache extends BasePredisCache
{
    private MigrationVersionService $migrationVersionService;

    private int $defaultLifeTime;

    private ?string $lastMigrationVersion = null;

    /**
     * @param ClientInterface         $client
     * @param MigrationVersionService $migrationVersionService
     * @param int                     $defaultLifeTime
     */
    public function __construct(ClientInterface $client, MigrationVersionService $migrationVersionService, $defaultLifeTime = 0)
    {
        parent::__construct($client);

        $this->migrationVersionService = $migrationVersionService;
        $this->defaultLifeTime = $defaultLifeTime;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return parent::doFetch($this->getModifiedKeyWithMigrationPrefix($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetchMultiple(array $keys): array
    {
        return parent::doFetchMultiple($this->getModifiedKeysWithMigrationPrefix($keys));
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id): bool
    {
        return parent::doContains($this->getModifiedKeyWithMigrationPrefix($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id): bool
    {
        return parent::doDelete($this->getModifiedKeyWithMigrationPrefix($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doDeleteMultiple(array $keys): bool
    {
        return parent::doDeleteMultiple($this->getModifiedKeysWithMigrationPrefix($keys));
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifetime = 0): bool
    {
        if (0 === $lifetime && 0 !== $this->defaultLifeTime) {
            $ttl = $this->defaultLifeTime;
        } else {
            $ttl = $lifetime;
        }

        return parent::doSave($this->getModifiedKeyWithMigrationPrefix($id), $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSaveMultiple(array $keysAndValues, $lifetime = 0): bool
    {
        if (0 === $lifetime && 0 !== $this->defaultLifeTime) {
            $ttl = $this->defaultLifeTime;
        } else {
            $ttl = $lifetime;
        }

        return parent::doSaveMultiple($this->getModifiedKeysAndValuesWithMigrationPrefix($keysAndValues), $ttl);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getModifiedKeyWithMigrationPrefix(string $key): string
    {
        if (null === $this->lastMigrationVersion) {
            $this->lastMigrationVersion = $this->migrationVersionService->getLastMigrationVersion();
        }

        return \sprintf('[%s]%s', $this->lastMigrationVersion, $key);
    }

    /**
     * @param array $keys
     *
     * @return array
     */
    private function getModifiedKeysWithMigrationPrefix(array $keys): array
    {
        return \array_map(
            function (string $key) {
                return $this->getModifiedKeyWithMigrationPrefix($key);
            },
            $keys
        );
    }

    /**
     * @param array $keysAndValues
     *
     * @return array
     */
    private function getModifiedKeysAndValuesWithMigrationPrefix(array $keysAndValues): array
    {
        $result = [];
        foreach ($keysAndValues as $key => $value) {
            $result[$this->getModifiedKeyWithMigrationPrefix($key)] = $value;
        }

        return $result;
    }
}
