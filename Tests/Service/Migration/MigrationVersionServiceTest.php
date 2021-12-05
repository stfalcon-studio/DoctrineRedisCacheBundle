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

namespace StfalconStudio\DoctrineRedisCacheBundle\Tests\Service\Migration;

use Doctrine\Migrations\Finder\MigrationFinder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StfalconStudio\DoctrineRedisCacheBundle\Service\Migration\MigrationVersionService;

/**
 * MigrationVersionServiceTest.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
final class MigrationVersionServiceTest extends TestCase
{
    private string $migrationDirectory = 'migrations';

    /** @var MigrationFinder|MockObject */
    private $migrationFinder;

    private MigrationVersionService $migrationVersionService;

    protected function setUp(): void
    {
        $this->migrationFinder = $this->createMock(MigrationFinder::class);
        $this->migrationVersionService = new MigrationVersionService($this->migrationDirectory, $this->migrationFinder);
    }

    protected function tearDown(): void
    {
        unset(
            $this->migrationFinder,
            $this->migrationVersionService
        );
    }

    public function testGetLastMigrationVersionForMultipleMigrations(): void
    {
        $this->migrationFinder
            ->expects(self::once())
            ->method('findMigrations')
            ->with($this->migrationDirectory)
            ->willReturn([
                '20190802110000' => 'path/to/file0',
                '20190802110001' => 'path/to/file1',
                '20190802110002' => 'path/to/file2',
            ])
        ;

        self::assertSame('20190802110002', $this->migrationVersionService->getLastMigrationVersion());
    }

    public function testGetLastMigrationVersionForOneMigration(): void
    {
        $this->migrationFinder
            ->expects(self::once())
            ->method('findMigrations')
            ->with($this->migrationDirectory)
            ->willReturn(['20190802110000' => 'path/to/file0'])
        ;

        self::assertSame('20190802110000', $this->migrationVersionService->getLastMigrationVersion());
    }

    public function testGetLastMigrationVersionForEmptyMigrations(): void
    {
        $this->migrationFinder
            ->expects(self::once())
            ->method('findMigrations')
            ->with($this->migrationDirectory)
            ->willReturn([])
        ;

        self::assertSame('0', $this->migrationVersionService->getLastMigrationVersion());
    }
}
