<?php
/*
 * This file is part of the StfalconStudioDoctrineRedisCacheBundle.
 *
 * (c) Stfalcon Studio <stfalcon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace StfalconStudio\DoctrineRedisCacheBundle\Service\Migration;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;

/**
 * MigrationVersionService.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class MigrationVersionService
{
    private $migrationDirectory;

    private $migrationFinder;

    /**
     * @param string                   $migrationDirectory
     * @param MigrationFinderInterface $migrationFinder
     */
    public function __construct(string $migrationDirectory, MigrationFinderInterface $migrationFinder)
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
