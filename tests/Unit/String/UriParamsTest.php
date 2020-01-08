<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\UriParams;
use PHPUnit\Framework\TestCase;

/**
 * Class UriParamsTest
 *
 * @package UtilsTests\Unit\String
 */
final class UriParamsTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\String\UriParams::parseOrderBy
     */
    public function testParseOrderBy(): void
    {
        self::assertEquals(['name' => 'DESC'], UriParams::parseOrderBy('name-'));
    }

}