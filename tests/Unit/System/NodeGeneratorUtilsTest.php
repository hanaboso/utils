<?php declare(strict_types=1);

namespace UtilsTests\Unit\System;

use Hanaboso\Utils\System\NodeGeneratorUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class NodeGeneratorUtilsTest
 *
 * @package UtilsTests\Unit\System
 */
final class NodeGeneratorUtilsTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::createServiceName
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::createNormalizedServiceName
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::normalizeName
     */
    public function testCreateServiceName(): void
    {
        self::assertEquals('1-nas-pro', NodeGeneratorUtils::createNormalizedServiceName('1', 'Náše produkty'));
    }

    /**
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::generateQueueName
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::createServiceName
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::createNormalizedServiceName
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::normalizeName
     */
    public function testGenerateQueueName(): void
    {
        self::assertEquals('pipes.1.2-nas-pro', NodeGeneratorUtils::generateQueueName('1', '2', 'Náše produkty'));
    }

    /**
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::generateQueueNameFromStrings
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::createServiceName
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::createNormalizedServiceName
     * @covers \Hanaboso\Utils\System\NodeGeneratorUtils::normalizeName
     */
    public function testGenerateQueueNameFromStrings(): void
    {
        self::assertEquals(
            'pipes.topology.node-nas-pro',
            NodeGeneratorUtils::generateQueueNameFromStrings('topology', 'node', 'Náše produkty'),
        );
    }

}
