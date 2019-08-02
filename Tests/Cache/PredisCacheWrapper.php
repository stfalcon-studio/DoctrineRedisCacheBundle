<?php
/*
 * This file is part of the StfalconStudioDoctrineRedisCacheBundle.
 *
 * (c) Stfalcon Studio <stfalcon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace StfalconStudio\DoctrineRedisCacheBundle\Tests\Cache;

use StfalconStudio\DoctrineRedisCacheBundle\Cache\PredisCache as BasePredisCache;

/**
 * PredisCacheWrapper.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
final class PredisCacheWrapper extends BasePredisCache
{
    /**
     * {@inheritdoc}
     */
    public function doFetch($id)
    {
        return parent::doFetch($id);
    }

    /**
     * {@inheritdoc}
     */
    public function doSave($id, $data, $lifeTime = 0): bool
    {
        return parent::doSave($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function doFetchMultiple(array $keys): array
    {
        return parent::doFetchMultiple($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function doSaveMultiple(array $keysAndValues, $lifetime = 0): bool
    {
        return parent::doSaveMultiple($keysAndValues, $lifetime);
    }

    /**
     * {@inheritdoc}
     */
    public function doContains($id): bool
    {
        return parent::doContains($id);
    }

    /**
     * {@inheritdoc}
     */
    public function doDelete($id): bool
    {
        return parent::doDelete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function doDeleteMultiple(array $keys): bool
    {
        return parent::doDeleteMultiple($keys);
    }
}
