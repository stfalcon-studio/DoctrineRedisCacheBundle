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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * StfalconStudioDoctrineRedisCacheExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class StfalconStudioDoctrineRedisCacheExtensionTest extends TestCase
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
        unset(
            $this->extension,
            $this->container,
        );
    }

    public function testLoadExtension(): void
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        self::assertSame('0', $this->container->getParameter('cache_prefix_seed'));
        self::assertArrayHasKey(MigrationFinder::class, $this->container->getRemovedIds());
        self::assertArrayNotHasKey(MigrationFinder::class, $this->container->getDefinitions());

        $this->expectException(ServiceNotFoundException::class);

        $this->container->get(MigrationFinder::class);
    }
}
