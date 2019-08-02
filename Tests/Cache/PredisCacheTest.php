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

namespace StfalconStudio\DoctrineRedisCacheBundle\Tests\Cache;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Predis\ClientInterface;
use StfalconStudio\DoctrineRedisCacheBundle\Service\Migration\MigrationVersionService;

/**
 * PredisCacheTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PredisCacheTest extends TestCase
{
    /** @var ClientInterface|MockObject */
    private $client;

    /** @var MigrationVersionService|MockObject */
    private $migrationVersionService;

    /** @var PredisCacheWrapper */
    private $predisCache;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->migrationVersionService = $this->createMock(MigrationVersionService::class);
        $this->migrationVersionService
            ->expects(self::once())
            ->method('getLastMigrationVersion')
            ->willReturn('123')
        ;

        $this->predisCache = new PredisCacheWrapper($this->client, $this->migrationVersionService);
    }

    protected function tearDown(): void
    {
        unset(
            $this->client,
            $this->migrationVersionService,
            $this->predisCache
        );
    }

    public function testFetch(): void
    {
        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('get'),
                $this->equalTo(['[123]test'])
            )
        ;

        $this->predisCache->doFetch('test');
    }

    public function testFetchMultiple(): void
    {
        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('mget'),
                $this->equalTo(['[123]testA', '[123]testB'])
            )
            ->willReturn(['s:6:"valueA";', 's:6:"valueB";'])
        ;

        $this->predisCache->doFetchMultiple(['testA', 'testB']);
    }

    public function testContains(): void
    {
        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('exists'),
                $this->equalTo(['[123]test'])
            )
        ;

        $this->predisCache->doContains('test');
    }

    public function testDelete(): void
    {
        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('del'),
                $this->equalTo(['[123]test'])
            )
        ;

        $this->predisCache->doDelete('test');
    }

    public function testDeleteMultiple(): void
    {
        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('del'),
                $this->equalTo([['[123]testA', '[123]testB']])
            )
        ;

        $this->predisCache->doDeleteMultiple(['testA', 'testB']);
    }

    public function testSaveWithoutTtl(): void
    {
        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('set'),
                $this->equalTo(['[123]test', 's:5:"value";'])
            )
        ;

        $this->predisCache->doSave('test', 'value');
    }

    public function testSaveWithTtl(): void
    {
        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('setex'),
                $this->equalTo(['[123]test', 456, 's:5:"value";'])
            )
        ;

        $this->predisCache->doSave('test', 'value', 456);
    }

    public function testSaveWithCustomDefaultTtl(): void
    {
        $this->predisCache = new PredisCacheWrapper($this->client, $this->migrationVersionService, 111);

        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('setex'),
                $this->equalTo(['[123]test', 111, 's:5:"value";'])
            )
        ;

        $this->predisCache->doSave('test', 'value');
    }

    public function testSaveMultipleWithTtl(): void
    {
        $this->client
            ->expects(self::at(0))
            ->method('__call')
            ->with(
                $this->equalTo('setex'),
                $this->equalTo(['[123]testA', 456, 's:6:"valueA";'])
            )
        ;
        $this->client
            ->expects(self::at(1))
            ->method('__call')
            ->with(
                $this->equalTo('setex'),
                $this->equalTo(['[123]testB', 456, 's:6:"valueB";'])
            )
        ;

        $this->predisCache->doSaveMultiple(['testA' => 'valueA', 'testB' => 'valueB'], 456);
    }

    public function testSaveMultipleWithoutTtl(): void
    {
        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                $this->equalTo('mset'),
                $this->equalTo([
                    [
                        '[123]testA' => 's:6:"valueA";',
                        '[123]testB' => 's:6:"valueB";',
                    ],
                ])
            )
        ;

        $this->predisCache->doSaveMultiple(['testA' => 'valueA', 'testB' => 'valueB']);
    }

    public function testSaveMultipleWithCustomTtl(): void
    {
        $this->predisCache = new PredisCacheWrapper($this->client, $this->migrationVersionService, 111);

        $this->client
            ->expects(self::at(0))
            ->method('__call')
            ->with(
                $this->equalTo('setex'),
                $this->equalTo(['[123]testA', 111, 's:6:"valueA";'])
            )
        ;
        $this->client
            ->expects(self::at(1))
            ->method('__call')
            ->with(
                $this->equalTo('setex'),
                $this->equalTo(['[123]testB', 111, 's:6:"valueB";'])
            )
        ;

        $this->predisCache->doSaveMultiple(['testA' => 'valueA', 'testB' => 'valueB']);
    }
}
