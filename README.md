# DoctrineRedisCacheBundle

:package: Add custom namespace for doctrine cache pools.

[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/stfalcon-studio/DoctrineRedisCacheBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/stfalcon-studio/DoctrineRedisCacheBundle/)
[![Build Status](https://img.shields.io/travis/stfalcon-studio/DoctrineRedisCacheBundle/master.svg?style=flat-square)](https://travis-ci.org/stfalcon-studio/DoctrineRedisCacheBundle)
[![CodeCov](https://img.shields.io/codecov/c/github/stfalcon-studio/DoctrineRedisCacheBundle.svg?style=flat-square)](https://codecov.io/github/stfalcon-studio/DoctrineRedisCacheBundle)
[![License](https://img.shields.io/packagist/l/stfalcon-studio/doctrine-redis-cache-bundle.svg?style=flat-square)](https://packagist.org/packages/stfalcon-studio/doctrine-redis-cache-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/stfalcon-studio/doctrine-redis-cache-bundle.svg?style=flat-square)](https://packagist.org/packages/stfalcon-studio/doctrine-redis-cache-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/stfalcon-studio/doctrine-redis-cache-bundle.svg?style=flat-square)](https://packagist.org/packages/stfalcon-studio/doctrine-redis-cache-bundle)
[![StyleCI](https://styleci.io/repos/200188496/shield?style=flat-square)](https://styleci.io/repos/200188496)

## Problem Solved By This Bundle

When you use Redis as cache provider to store Doctrine _query/metadata/result/second level_ cache, Doctrine generates unique keys for each cache item.
When you change your database schema, create a new migration (Doctrine migration) and then deploy it to production, you have to clean your Doctrine cache after deploy.
Doctrine has console commands to clean any type of cache and they work well. But if during the cache flushing, you have already running script (long running console/cron task or consumer) it still uses old schema info which can conflict with your new schema.
In this case this script can regenerate cache (because it has been already flushed) with old schema metadata, query, result etc.

To prevent this problem, we add a custom **namespace** for each selected cache pool. This **namespace** is a name of the last migration version.
For example, you deploy the first version of your project to production. Last migration version is `1` so all keys in cache will have prefix `[1]` (e.g. `[1]hash_by_doctrine`).
Then you modify your schema, generate a new migration (version `2`) and deploy it to production. Old running script will still use and generate keys with prefix `[1]`, but new scripts will begin to use fresh prefix `[2]` and don't conflict with previous prefix.

After that you can stop or rerun old script. And after rerun they will use a new prefix and you can clean cache entries with the previous prefix.

## Installation

```composer req stfalcon-studio/doctrine-redis-cache-bundle```

#### Check the `config/bundles.php` file

By default Symfony Flex will add this bundle to the `config/bundles.php` file.
But in case when you ignored `contrib-recipe` during bundle installation it would not be added. In this case add the bundle manually.

```php
# config/bundles.php

return [
    // Other bundles...
    StfalconStudio\DoctrineRedisCacheBundle\StfalconStudioDoctrineRedisCacheBundle::class => ['all' => true],
    // Other bundles...
];
```

### Example of possible cache pool configuration

```yaml
framework:
    cache:
        default_redis_provider: snc_redis.default
        pools:
            doctrine.result_cache_pool:
                adapter: cache.adapter.redis
                provider: snc_redis.doctrine_result_cache
            doctrine.metadata_cache_pool:
                adapter: cache.adapter.redis
                provider: snc_redis.doctrine_metadata_cache
            doctrine.query_cache_pool:
                adapter: cache.adapter.redis
                provider: snc_redis.doctrine_query_cache
```

### Bundle configuration

```yaml
stfalcon_studio_doctrine_redis_cache:
    cache_pools:
        - 'doctrine.query_cache_pool'
        - 'doctrine.metadata_cache_pool'
        - 'doctrine.result_cache_pool'

```

## Contributing

Read the [CONTRIBUTING](https://github.com/stfalcon-studio/DoctrineRedisCacheBundle/blob/master/.github/CONTRIBUTING.md) file.
