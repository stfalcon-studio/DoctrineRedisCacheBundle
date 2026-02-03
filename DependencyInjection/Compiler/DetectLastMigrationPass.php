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

namespace StfalconStudio\DoctrineRedisCacheBundle\DependencyInjection\Compiler;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Finder\MigrationFinder;
use StfalconStudio\DoctrineRedisCacheBundle\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * DetectLastMigrationPass.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class DetectLastMigrationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $migrationFinder = $container->get(MigrationFinder::class);
        if (!$migrationFinder instanceof MigrationFinder) {
            throw new InvalidArgumentException(\sprintf('Service "%s" is missed in container', MigrationFinder::class));
        }

        $configuration = $container->get('doctrine.migrations.configuration');
        if (!$configuration instanceof Configuration) {
            throw new InvalidArgumentException(\sprintf('Service "%s" is missed in container', Configuration::class));
        }

        $migrations = [];
        foreach ($configuration->getMigrationDirectories() as $migrationDirectory) {
            foreach ($migrationFinder->findMigrations($migrationDirectory) as $migration) {
                $migrations[] = $migration;
            }
        }
        foreach ($configuration->getMigrationClasses() as $migration) {
            $migrations[] = $migration;
        }

        $processedMigrations = [];
        foreach ($migrations as $migration) {
            preg_match('#Version.*#', $migration, $matches);
            if (!empty($matches[0])) {
                $processedMigrations[] = $matches[0];
            }
        }

        sort($processedMigrations); // Sort by name
        $hash = '';
        if (!empty($processedMigrations)) {
            $hash = md5(implode('', $processedMigrations));
        }

        $cachePools = [];
        if ($container->hasParameter('doctrine_redis_cache.cache_pools')) {
            $cachePools = (array) $container->getParameter('doctrine_redis_cache.cache_pools');
        }

        if (!empty($hash) && !empty($cachePools)) {
            foreach ($cachePools as $cachePool) {
                $definition = $container->getDefinition($cachePool);
                $tags = $definition->getTags();
                $tags['cache.pool'][0]['namespace'] = $hash;
                $definition->setTags($tags);
            }
        }
    }
}
