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

use PHPUnit\Framework\TestCase;
use StfalconStudio\DoctrineRedisCacheBundle\DependencyInjection\Compiler\DetectLastMigrationPass;
use StfalconStudio\DoctrineRedisCacheBundle\StfalconStudioDoctrineRedisCacheBundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * StfalconStudioDoctrineRedisCacheExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class StfalconStudioDoctrineRedisCacheBundleTest extends TestCase
{
    public function testBuild(): void
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder
            ->expects(self::once())
            ->method('addCompilerPass')
            ->with(self::isInstanceOf(DetectLastMigrationPass::class), PassConfig::TYPE_BEFORE_OPTIMIZATION, 33)
        ;

        $bundle = new StfalconStudioDoctrineRedisCacheBundle();
        $bundle->build($containerBuilder);
    }
}
