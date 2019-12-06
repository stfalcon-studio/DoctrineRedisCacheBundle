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

namespace StfalconStudio\DoctrineRedisCacheBundle\Service\Migration;

use Doctrine\Migrations\Finder\MigrationFinder;

/**
 * MigrationVersionService.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class MigrationVersionService
{
    /** @var string */
    private $migrationDirectory;

    /** @var MigrationFinder */
    private $migrationFinder;

    /**
     * @param string          $migrationDirectory
     * @param MigrationFinder $migrationFinder
     */
    public function __construct(string $migrationDirectory, MigrationFinder $migrationFinder)
    {
        $this->migrationDirectory = $migrationDirectory;
        $this->migrationFinder = $migrationFinder;
    }

    /**
     * @return string
     */
    public function getLastMigrationVersion(): string
    {
        $migrations = $this->migrationFinder->findMigrations($this->migrationDirectory);
        $versions = \array_keys($migrations);
        $latest = \end($versions);

        return false !== $latest ? (string) $latest : '0';
    }
}
