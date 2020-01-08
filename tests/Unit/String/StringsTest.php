<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\Strings;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class StringsTest
 *
 * @package UtilsTests\Unit\String
 */
final class StringsTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\String\Strings::trim
     */
    public function testTrim(): void
    {
        self::assertEquals('These are a few words', Strings::trim('        These are a few words//', ' //'));
    }

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

    /**
     * @covers \Hanaboso\Utils\String\Strings::endsWith
     */
    public function testEndsWith(): void
    {
        self::assertEquals(TRUE, Strings::endsWith('name', 'me'));
    }

    /**
     * @covers \Hanaboso\Utils\String\Strings::webalize
     * @covers \Hanaboso\Utils\String\Strings::toAscii
     * @covers \Hanaboso\Utils\String\Strings::iconv
     */
    public function testWebalize(): void
    {
        self::assertEquals('nas-produkt', Strings::webalize('Náš produkt'));
    }

    /**
     * @covers \Hanaboso\Utils\String\Strings::glibc
     *
     * @throws ReflectionException
     */
    public function testGlibc(): void
    {
        $strings = new ReflectionClass(Strings::class);
        $method  = $strings->getMethod('glibc');
        $method->setAccessible(TRUE);

        self::assertEquals('Nas produkt', $method->invokeArgs(NULL, ['Náš produkt']));
    }

}
