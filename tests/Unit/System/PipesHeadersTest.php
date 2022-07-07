<?php declare(strict_types=1);

namespace UtilsTests\Unit\System;

use Hanaboso\Utils\System\PipesHeaders;
use PHPUnit\Framework\TestCase;

/**
 * Class PipesHeadersTest
 *
 * @package UtilsTests\Unit\System
 */
final class PipesHeadersTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\System\PipesHeaders::get()
     */
    public function testGet(): void
    {
        self::assertSame(
            '456',
            PipesHeaders::get(
                'token',
                [
                    'content-type' => 'application/json', 'node-id' => '123', 'token' => ['456'],
                ],
            ),
        );
    }

    /**
     * @covers \Hanaboso\Utils\System\PipesHeaders::debugInfo()
     */
    public function testDebugInfo(): void
    {
        self::assertSame(
            [
                'node-id'        => '123',
                'correlation-id' => '456',
            ],
            PipesHeaders::debugInfo(
                [
                    'content-type'      => 'application/json',
                    'node-id'        => '123',
                    'token'          => '456',
                    'correlation-id' => '456',
                ],
            ),
        );
    }

}
