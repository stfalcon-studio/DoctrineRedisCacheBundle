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
use Symfony\Component\DependencyInjection\ContainerBuilder;

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

        $this->container
            ->expects(self::once())
            ->method('setParameter')
            ->with('cache_prefix_seed', 'Version20200101000003')
        ;

        $this->detectLastMigrationPass->process($this->container);
    }
}
