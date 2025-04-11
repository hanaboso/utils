<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonTest
 *
 * @package UtilsTests\Unit\String
 */
#[CoversClass(Json::class)]
final class JsonTest extends TestCase
{

    /**
     * @return void
     */
    public function testEncode(): void
    {
        self::assertSame('{"1":"example1","2":"example2"}', Json::encode([1 => 'example1', 2 => 'example2']));
    }

    /**
     * @return void
     */
    public function testDecode(): void
    {
        self::assertEquals([1 => 'example1', 2 => 'example2'], Json::decode('{"1":"example1","2":"example2"}'));
    }

}
