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

namespace StfalconStudio\DoctrineRedisCacheBundle\Tests\Cache;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\ClientInterface;

/**
 * PredisCacheTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PredisCacheTest extends TestCase
{
    /** @var ClientInterface|MockObject */
    private $client;

    private PredisCacheWrapper $predisCache;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);

        $this->predisCache = new PredisCacheWrapper($this->client, '123');
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
                self::equalTo('get'),
                self::equalTo(['[123]test'])
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
                self::equalTo('mget'),
                self::equalTo(['[123]testA', '[123]testB'])
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
                self::equalTo('exists'),
                self::equalTo(['[123]test'])
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
                self::equalTo('del'),
                self::equalTo(['[123]test'])
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
                self::equalTo('del'),
                self::equalTo([['[123]testA', '[123]testB']])
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
                self::equalTo('set'),
                self::equalTo(['[123]test', 's:5:"value";'])
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
                self::equalTo('setex'),
                self::equalTo(['[123]test', 456, 's:5:"value";'])
            )
        ;

        $this->predisCache->doSave('test', 'value', 456);
    }

    public function testSaveWithCustomDefaultTtl(): void
    {
        $this->predisCache = new PredisCacheWrapper($this->client, '123', 111);

        $this->client
            ->expects(self::once())
            ->method('__call')
            ->with(
                self::equalTo('setex'),
                self::equalTo(['[123]test', 111, 's:5:"value";'])
            )
        ;

        $this->predisCache->doSave('test', 'value');
    }

    public function testSaveMultipleWithTtl(): void
    {
        $this->client
            ->expects(self::exactly(2))
            ->method('__call')
            ->withConsecutive(
                [
                    self::equalTo('setex'),
                    self::equalTo(['[123]testA', 456, 's:6:"valueA";']),
                ],
                [
                    self::equalTo('setex'),
                    self::equalTo(['[123]testB', 456, 's:6:"valueB";']),
                ]
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
                self::equalTo('mset'),
                self::equalTo([
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
        $this->predisCache = new PredisCacheWrapper($this->client, '123', 111);

        $this->client
            ->expects(self::exactly(2))
            ->method('__call')
            ->withConsecutive(
                [
                    self::equalTo('setex'),
                    self::equalTo(['[123]testA', 111, 's:6:"valueA";']),
                ],
                [
                    self::equalTo('setex'),
                    self::equalTo(['[123]testB', 111, 's:6:"valueB";']),
                ]
            )
        ;

        $this->predisCache->doSaveMultiple(['testA' => 'valueA', 'testB' => 'valueB']);
    }
}
