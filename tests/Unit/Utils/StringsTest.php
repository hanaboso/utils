<?php declare(strict_types=1);

namespace UtilsTests\Unit\Utils;

use Hanaboso\Utils\String\Strings;
use PHPUnit\Framework\TestCase;

/**
 * Class StringsTest
 *
 * @package UtilsTests\Unit\Utils
 */
final class StringsTest extends TestCase
{

    /**
     * @dataProvider toCamelCaseDataProvider
     *
     * @param string $string
     * @param string $assert
     * @param bool   $firstUpper
     *
     * @covers       \Hanaboso\Utils\String\Strings::toCamelCase()
     */
    public function testToCamelCase(string $string, string $assert, bool $firstUpper): void
    {
        $camelCase = Strings::toCamelCase($string, $firstUpper);
        self::assertSame($assert, $camelCase);
    }

    /**
     * @return mixed[]
     */
    public function toCamelCaseDataProvider(): array
    {
        return [
            [
                'some_group',
                'SomeGroup',
                FALSE,
            ],
            [
                'some_group',
                'someGroup',
                TRUE,
            ],
            [
                'some_group_some_group',
                'someGroupSomeGroup',
                TRUE,
            ],
        ];
    }

    /**
     * @covers \Hanaboso\Utils\String\Strings::getShortClassName()
     */
    public function testGetShortClassName(): void
    {
        self::assertSame('StringsTest', Strings::getShortClassName($this));
    }

}
