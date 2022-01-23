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

namespace StfalconStudio\DoctrineRedisCacheBundle\Tests\DependencyInjection\Exception;

use PHPUnit\Framework\TestCase;
use StfalconStudio\DoctrineRedisCacheBundle\Exception\InvalidArgumentException;

/**
 * InvalidArgumentExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class InvalidArgumentExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $exception = new InvalidArgumentException();

        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
    }
}
