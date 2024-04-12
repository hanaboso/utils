<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\Base64;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class Base64Test
 *
 * @package UtilsTests\Unit\String
 */
#[CoversClass(Base64::class)]
final class Base64Test extends TestCase
{

    /**
     * @return void
     */
    public function testBase64UrlEncode(): void
    {
        self::assertEquals('ZXhhbXBsZQ,,', Base64::base64UrlEncode('example'));
    }

    /**
     * @return void
     */
    public function testBase64UrlDecode(): void
    {
        self::assertEquals('example', Base64::base64UrlDecode('ZXhhbXBsZQ,,'));
    }

}
