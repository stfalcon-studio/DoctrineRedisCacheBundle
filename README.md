# DoctrineRedisCacheBundle

:package: Custom implementation of Predis cache provider for Doctrine cache.

[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/stfalcon-studio/DoctrineRedisCacheBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/stfalcon-studio/DoctrineRedisCacheBundle/)
[![Build Status](https://img.shields.io/travis/stfalcon-studio/DoctrineRedisCacheBundle/master.svg?style=flat-square)](https://travis-ci.org/stfalcon-studio/DoctrineRedisCacheBundle)
[![CodeCov](https://img.shields.io/codecov/c/github/stfalcon-studio/DoctrineRedisCacheBundle.svg?style=flat-square)](https://codecov.io/github/stfalcon-studio/DoctrineRedisCacheBundle)
[![License](https://img.shields.io/packagist/l/stfalcon-studio/doctrine-redis-cache-bundle.svg?style=flat-square)](https://packagist.org/packages/stfalcon-studio/doctrine-redis-cache-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/stfalcon-studio/doctrine-redis-cache-bundle.svg?style=flat-square)](https://packagist.org/packages/stfalcon-studio/doctrine-redis-cache-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/stfalcon-studio/doctrine-redis-cache-bundle.svg?style=flat-square)](https://packagist.org/packages/stfalcon-studio/doctrine-redis-cache-bundle)
[![StyleCI](https://styleci.io/repos/200188496/shield?style=flat-square)](https://styleci.io/repos/200188496)

[![SymfonyInsight](https://insight.symfony.com/projects/c76a1cb8-0947-4fc2-8d65-a1a2e802a523/big.svg)](https://insight.symfony.com/projects/c76a1cb8-0947-4fc2-8d65-a1a2e802a523)

## Problem Solved By This Bundle

When you use Redis as cache provider to store Doctrine _query/metadata/result/second level_ cache, Doctrine generates unique keys for each cache item.
When you change your database schema, create a new migration (Doctrine migration) and then deploy it to production, you have to clean your Doctrine cache after deploy.
Doctrine has console commands to clean any type of cache and they work well. But if during the cache flushing, you have already running script (long running console/cron task or consumer) it still uses old schema info which can conflict with your new schema.
In this case this script can regenerate cache (because it has been already flushed) with old schema metadata, query, result etc.

To prevent this problem, we modified `PredisCache` class from Doctrine Cache library and added a **prefix** to keys which are used by Doctrine. This **prefix** is a NUMBER of the last migration version.
For example, you deploy the first version of your project to production. Last migration version is `1` so all keys in cache will have prefix `[1]` (e.g. `[1]hash_by_doctrine`).
Then you modify your schema, generate a new migration (version `2`) and deploy it to production. Old running script will still use and generate keys with prefix `[1]`, but new scripts will begin to use fresh prefix `[2]` and don't conflict with previous prefix.

After that you can stop or rerun old script. And after rerun they will use a new prefix and you can clean cache entries with the previous prefix.

## Installation

```composer req stfalcon-studio/doctrine-redis-cache-bundle='~1.1'```

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

#### Override `PredisCache` service with custom implementation

Open the file `config/services.yaml` and add there next lines under the `services` section:

```yaml
services:
    doctrine_cache.abstract.predis:
        class: StfalconStudio\DoctrineRedisCacheBundle\Cache\PredisCache
```

## Contributing

Read the [CONTRIBUTING](https://github.com/stfalcon-studio/DoctrineRedisCacheBundle/blob/master/.github/CONTRIBUTING.md) file.
