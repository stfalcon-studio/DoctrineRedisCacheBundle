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

namespace StfalconStudio\DoctrineRedisCacheBundle\DependencyInjection;

use Doctrine\Migrations\Finder\RecursiveRegexFinder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages StfalconStudioDoctrineRedisCacheBundle configuration.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class StfalconStudioDoctrineRedisCacheExtension extends Extension
{
    private RecursiveRegexFinder $migrationFinder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->migrationFinder = new RecursiveRegexFinder();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $dirName = $container->getParameter('doctrine_migrations.dir_name');

        if (\is_string($dirName)) {
            $container->setParameter('cache_prefix_seed', $this->getLastMigrationVersion($dirName));
        }
    }

    /**
     * @param string $dir
     *
     * @return string
     */
    public function getLastMigrationVersion(string $dir): string
    {
        $migrations = $this->migrationFinder->findMigrations($dir);

        $versions = \array_keys($migrations);
        $latest = \end($versions);

        return false !== $latest ? (string) $latest : '0';
    }
}
