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

    /**
     * @covers \Hanaboso\Utils\System\PipesHeaders::decorateLimitKey()
     */
    public function testDecorateLimitKey(): void
    {
        self::assertSame('limitKey|', PipesHeaders::decorateLimitKey('limitKey'));
    }

    /**
     * @covers \Hanaboso\Utils\System\PipesHeaders::decorateLimitKey()
     */
    public function testDecorateLimitKeySame(): void
    {
        self::assertSame('limitKey|', PipesHeaders::decorateLimitKey('limitKey|'));
    }

    /**
     * @covers \Hanaboso\Utils\System\PipesHeaders::parseLimitKey()
     */
    public function testParseKey(): void
    {
        self::assertSame([], PipesHeaders::parseLimitKey(NULL));
    }

    /**
     * @covers \Hanaboso\Utils\System\PipesHeaders::getLimiterKey()
     * @covers \Hanaboso\Utils\System\PipesHeaders::parseLimitKey()
     */
    public function testGetAndParseLimitKey(): void
    {
        $limiterKey = PipesHeaders::getLimiterKey('limiterKey',60,10);
        self::assertSame('limiterKey|;60;10', $limiterKey);
        $parsedKey = PipesHeaders::parseLimitKey($limiterKey);
        self::assertSame(['limiterKey|' => 'limiterKey|;60;10'], $parsedKey);
    }

    /**
     * @covers \Hanaboso\Utils\System\PipesHeaders::getLimiterKeyWithGroup()
     * @covers \Hanaboso\Utils\System\PipesHeaders::parseLimitKey()
     */
    public function testGetAndParseLimitKeyWithGroup(): void
    {
        $limiterKey = PipesHeaders::getLimiterKeyWithGroup('limiterKey',60,10,'groupLimiterKey',61,11);
        self::assertSame('limiterKey|;60;10;groupLimiterKey|;61;11', $limiterKey);
        $parsedKey = PipesHeaders::parseLimitKey($limiterKey);
        self::assertSame([
            'limiterKey|' => 'limiterKey|;60;10',
            'groupLimiterKey|' => 'groupLimiterKey|;61;11',
            ], $parsedKey);
    }

}
