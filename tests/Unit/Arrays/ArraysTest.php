<?php declare(strict_types=1);

namespace UtilsTests\Unit\Arrays;

use Hanaboso\Utils\Arrays\Arrays;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class ArraysTest
 *
 * @package UtilsTests\Unit\Arrays
 */
#[CoversClass(Arrays::class)]
final class ArraysTest extends TestCase
{

    /**
     * @return void
     */
    public function testIsList(): void
    {
        self::assertTrue(Arrays::isList(['a', 'b', 'c']));
    }

    /**
     * @return void
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
