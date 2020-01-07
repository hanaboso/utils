<?php declare(strict_types=1);

namespace UtilsTests\Unit\Utils;

use Hanaboso\Utils\Utils\PipesHeaders;
use PHPUnit\Framework\TestCase;

/**
 * Class PipesHeadersTest
 *
 * @package UtilsTests\Unit\Utils
 */
final class PipesHeadersTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\Utils\PipesHeaders::createKey()
     */
    public function testCreateKey(): void
    {
        self::assertSame('pf-node-id', PipesHeaders::createKey('node-id'));
    }

    /**
     * @covers \Hanaboso\Utils\Utils\PipesHeaders::clear()
     */
    public function testClear(): void
    {
        self::assertSame(
            ['content-type' => 'application/json', 'pf-token' => '456'],
            PipesHeaders::clear(
                [
                    'content-type' => 'application/json', 'pfp-node-id' => '123', 'pf-token' => '456',
                ]
            )
        );
    }

    /**
     * @covers \Hanaboso\Utils\Utils\PipesHeaders::get()
     */
    public function testGet(): void
    {
        self::assertSame(
            '456',
            PipesHeaders::get(
                'token',
                [
                    'content-type' => 'application/json', 'pfp-node-id' => '123', 'pf-token' => '456',
                ]
            )
        );
    }

    /**
     * @covers \Hanaboso\Utils\Utils\PipesHeaders::debugInfo()
     */
    public function testDebugInfo(): void
    {
        self::assertSame(
            [
                'node_id'        => '123',
                'correlation_id' => '456',
            ],
            PipesHeaders::debugInfo(
                [
                    'content-type'      => 'application/json',
                    'pf-node-id'        => '123',
                    'pf-token'          => '456',
                    'pf-correlation-id' => '456',
                ]
            )
        );
    }

}
