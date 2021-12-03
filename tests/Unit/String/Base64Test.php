<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\Base64;
use PHPUnit\Framework\TestCase;

/**
 * Class Base64Test
 *
 * @package UtilsTests\Unit\String
 */
final class Base64Test extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\String\Base64::base64UrlEncode
     */
    public function testBase64UrlEncode(): void
    {
        self::assertEquals('ZXhhbXBsZQ,,', Base64::base64UrlEncode('example'));
    }

    /**
     * @covers \Hanaboso\Utils\String\Base64::base64UrlDecode
     */
    public function testBase64UrlDecode(): void
    {
        self::assertEquals('example', Base64::base64UrlDecode('ZXhhbXBsZQ,,'));
    }

}
