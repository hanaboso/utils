<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\Json;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonTest
 *
 * @package UtilsTests\Unit\String
 */
final class JsonTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\String\Json::encode
     */
    public function testEncode(): void
    {
        self::assertEquals('{"1":"example1","2":"example2"}', Json::encode([1 => 'example1', 2 => 'example2']));
    }

    /**
     * @covers \Hanaboso\Utils\String\Json::decode
     */
    public function testDecode(): void
    {
        self::assertEquals([1 => 'example1', 2 => 'example2'], Json::decode('{"1":"example1","2":"example2"}'));
    }

}