<?php declare(strict_types=1);

namespace UtilsTests\Unit\System;

use Hanaboso\Utils\System\NodeGeneratorUtils;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class NodeGeneratorUtilsTest
 *
 * @package UtilsTests\Unit\System
 */
#[CoversClass(NodeGeneratorUtils::class)]
final class NodeGeneratorUtilsTest extends TestCase
{

    /**
     * @return void
     */
    public function testCreateServiceName(): void
    {
        self::assertSame('1-nas-pro', NodeGeneratorUtils::createNormalizedServiceName('1', 'Náše produkty'));
    }

    /**
     * @return void
     */
    public function testGenerateQueueName(): void
    {
        self::assertSame('pipes.1.2-nas-pro', NodeGeneratorUtils::generateQueueName('1', '2', 'Náše produkty'));
    }

    /**
     * @return void
     */
    public function testGenerateQueueNameFromStrings(): void
    {
        self::assertSame(
            'pipes.topology.node-nas-pro',
            NodeGeneratorUtils::generateQueueNameFromStrings('topology', 'node', 'Náše produkty'),
        );
    }

}
