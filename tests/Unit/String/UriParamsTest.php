<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\UriParams;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class UriParamsTest
 *
 * @package UtilsTests\Unit\String
 */
#[CoversClass(UriParams::class)]
final class UriParamsTest extends TestCase
{

    /**
     * @return void
     */
    public function testParseOrderBy(): void
    {
        self::assertEquals(['name' => 'DESC'], UriParams::parseOrderBy('name-'));
    }

}
