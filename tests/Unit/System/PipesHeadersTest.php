<?php declare(strict_types=1);

namespace UtilsTests\Unit\System;

use Hanaboso\Utils\System\PipesHeaders;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class PipesHeadersTest
 *
 * @package UtilsTests\Unit\System
 */
#[CoversClass(PipesHeaders::class)]
final class PipesHeadersTest extends TestCase
{

    /**
     * @return void
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
     * @return void
     */
    public function testDebugInfo(): void
    {
        self::assertSame(
            [
                'correlation-id' => '456',
                'node-id'        => '123',
            ],
            PipesHeaders::debugInfo(
                [
                    'content-type'      => 'application/json',
                    'correlation-id' => '456',
                    'node-id'        => '123',
                    'token'          => '456',
                ],
            ),
        );
    }

    /**
     * @return void
     */
    public function testDecorateLimitKey(): void
    {
        self::assertSame('limitKey|', PipesHeaders::decorateLimitKey('limitKey'));
    }

    /**
     * @return void
     */
    public function testDecorateLimitKeySame(): void
    {
        self::assertSame('limitKey|', PipesHeaders::decorateLimitKey('limitKey|'));
    }

    /**
     * @return void
     */
    public function testParseKey(): void
    {
        self::assertSame([], PipesHeaders::parseLimitKey(NULL));
    }

    /**
     * @return void
     */
    public function testGetAndParseLimitKey(): void
    {
        $limiterKey = PipesHeaders::getLimiterKey('limiterKey',60,10);
        self::assertSame('limiterKey|;60;10', $limiterKey);
        $parsedKey = PipesHeaders::parseLimitKey($limiterKey);
        self::assertSame(['limiterKey|' => 'limiterKey|;60;10'], $parsedKey);
    }

    /**
     * @return void
     */
    public function testGetAndParseLimitKeyWithGroup(): void
    {
        $limiterKey = PipesHeaders::getLimiterKeyWithGroup('limiterKey',60,10,'groupLimiterKey',61,11);
        self::assertSame('limiterKey|;60;10;groupLimiterKey|;61;11', $limiterKey);
        $parsedKey = PipesHeaders::parseLimitKey($limiterKey);
        // @codingStandardsIgnoreLine
        self::assertSame(['limiterKey|' => 'limiterKey|;60;10', 'groupLimiterKey|' => 'groupLimiterKey|;61;11'], $parsedKey);
    }

}
