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

namespace StfalconStudio\DoctrineRedisCacheBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Finder\MigrationFinder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StfalconStudio\DoctrineRedisCacheBundle\DependencyInjection\Compiler\DetectLastMigrationPass;
use StfalconStudio\DoctrineRedisCacheBundle\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * DetectLastMigrationPassTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class DetectLastMigrationPassTest extends TestCase
{
    /** @var MigrationFinder|MockObject */
    private $migrationFinder;

    /** @var ContainerBuilder|MockObject */
    private $container;

    private Configuration $configuration;

    private DetectLastMigrationPass $detectLastMigrationPass;

    protected function setUp(): void
    {
        $this->migrationFinder = $this->createMock(MigrationFinder::class);
        $this->configuration = new Configuration();
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->detectLastMigrationPass = new DetectLastMigrationPass();
    }

    protected function tearDown(): void
    {
        unset(
            $this->migrationFinder,
            $this->configuration,
            $this->container,
            $this->detectLastMigrationPass,
        );
    }

    public function testProcess(): void
    {
        $this->container
            ->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([MigrationFinder::class], ['doctrine.migrations.configuration'])
            ->willReturnOnConsecutiveCalls($this->migrationFinder, $this->configuration)
        ;

        $this->configuration->addMigrationsDirectory('TestMigrations', __DIR__.'../../Migrations');
        $this->configuration->addMigrationClass(Version20200101000003::class);

        $this->migrationFinder
            ->expects(self::once())
            ->method('findMigrations')
            ->with(__DIR__.'../../Migrations')
            ->willReturn(['Version20200101000001', 'Version20200101000002'])
        ;

        $this->container
            ->expects(self::once())
            ->method('hasParameter')
            ->with('doctrine_redis_cache.cache_pools')
            ->willReturn(true)
        ;

        $this->container
            ->expects(self::once())
            ->method('getParameter')
            ->with('doctrine_redis_cache.cache_pools')
            ->willReturn(['foo'])
        ;

        $definition = $this->createMock(Definition::class);

        $this->container
            ->expects(self::once())
            ->method('getDefinition')
            ->with('foo')
            ->willReturn($definition)
        ;

        $definition
            ->expects(self::once())
            ->method('getTags')
            ->willReturn([])
        ;

        $definition
            ->expects(self::once())
            ->method('setTags')
            ->willReturn(['cache.pool' => ['namespace' => 'Version20200101000003']])
        ;

        $this->detectLastMigrationPass->process($this->container);
    }

    public function testProcessMissingMigrationFinder(): void
    {
        $this->container
            ->expects(self::once())
            ->method('get')
            ->with(MigrationFinder::class)
            ->willReturn(null)
        ;

        $this->expectException(InvalidArgumentException::class);

        $this->detectLastMigrationPass->process($this->container);
    }

    public function testProcessMissingMigrationsConfiguration(): void
    {
        $this->container
            ->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([MigrationFinder::class], ['doctrine.migrations.configuration'])
            ->willReturnOnConsecutiveCalls($this->migrationFinder, null)
        ;

        $this->expectException(InvalidArgumentException::class);

        $this->detectLastMigrationPass->process($this->container);
    }
}
