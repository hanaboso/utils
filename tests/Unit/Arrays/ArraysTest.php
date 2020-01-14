<?php declare(strict_types=1);

namespace UtilsTests\Unit\Arrays;

use Hanaboso\Utils\Arrays\Arrays;
use PHPUnit\Framework\TestCase;

/**
 * Class ArraysTest
 *
 * @package UtilsTests\Unit\Arrays
 */
final class ArraysTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\Arrays\Arrays::isList
     */
    public function testIsList(): void
    {
        self::assertTrue(Arrays::isList(['a', 'b', 'c']));
    }

}
