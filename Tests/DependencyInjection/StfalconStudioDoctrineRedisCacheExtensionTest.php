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

namespace StfalconStudio\DoctrineRedisCacheBundle\Tests\DependencyInjection;

use Doctrine\Migrations\Finder\MigrationFinder;
use PHPUnit\Framework\TestCase;
use StfalconStudio\DoctrineRedisCacheBundle\DependencyInjection\StfalconStudioDoctrineRedisCacheExtension;
use StfalconStudio\DoctrineRedisCacheBundle\Service\Migration\MigrationVersionService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * StfalconStudioDoctrineRedisCacheExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class StfalconStudioDoctrineRedisCacheExtensionTest extends TestCase
{
    private StfalconStudioDoctrineRedisCacheExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new StfalconStudioDoctrineRedisCacheExtension();
        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    protected function tearDown(): void
    {
        unset($this->extension, $this->container);
    }

    public function testLoadExtension(): void
    {
        $this->container->setParameter('doctrine_migrations.dir_name', __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Migrations');
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        self::assertSame('20200101000001', $this->container->getParameter('cache_prefix_seed'));

        self::assertArrayHasKey(MigrationVersionService::class, $this->container->getRemovedIds());
        self::assertArrayHasKey(MigrationFinder::class, $this->container->getRemovedIds());

        self::assertArrayNotHasKey(MigrationVersionService::class, $this->container->getDefinitions());
        self::assertArrayNotHasKey(MigrationFinder::class, $this->container->getDefinitions());

        $this->expectException(ServiceNotFoundException::class);

        $this->container->get(MigrationVersionService::class);
        $this->container->get(MigrationFinder::class);
    }

    public function testLoadExtensionWithNotMigration(): void
    {
        $this->container->setParameter('doctrine_migrations.dir_name', __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Migrations'.\DIRECTORY_SEPARATOR.'empty');
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        self::assertSame('0', $this->container->getParameter('cache_prefix_seed'));
    }
}
