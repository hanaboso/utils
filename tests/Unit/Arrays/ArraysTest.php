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

    /**
     * @covers \Hanaboso\Utils\Arrays\Arrays::diff
     * @covers \Hanaboso\Utils\Arrays\Arrays::getCreatedKeys
     * @covers \Hanaboso\Utils\Arrays\Arrays::getUpdatedKeys
     * @covers \Hanaboso\Utils\Arrays\Arrays::getRemovedKeys
     */
    public function testDiff(): void
    {
        self::assertEquals(
            [
                'created' => [
                    'c' => 'c',
                ],
                'deleted' => [
                    'a' => 'a',
                ],
                'updated' => [
                    'b' => [
                        'updated' => [
                            [
                                'updated' => [
                                    'b' => [
                                        'new' => 'b2',
                                        'old' => 'b1',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Arrays::diff(
                [
                    'a' => 'a',
                    'b' => [
                        [
                            'a' => 'a',
                            'b' => 'b1',
                        ],
                    ],
                ],
                [
                    'b' => [
                        [
                            'a' => 'a',
                            'b' => 'b2',
                        ],
                    ],
                    'c' => 'c',
                ],
            ),
        );
    }

}
