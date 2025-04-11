<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\Strings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class StringsTest
 *
 * @package UtilsTests\Unit\String
 */
#[CoversClass(Strings::class)]
final class StringsTest extends TestCase
{

    /**
     * @return void
     */
    public function testTrim(): void
    {
        self::assertSame('These are a few words', Strings::trim('        These are a few words//', ' //'));
    }

    /**
     * @param string $string
     * @param string $assert
     * @param bool   $firstUpper
     */
    #[DataProvider('toCamelCaseDataProvider')]
    public function testToCamelCase(string $string, string $assert, bool $firstUpper): void
    {
        $camelCase = Strings::toCamelCase($string, $firstUpper);
        self::assertSame($assert, $camelCase);
    }

    /**
     * @return void
     */
    public function testGetShortClassName(): void
    {
        self::assertSame('StringsTest', Strings::getShortClassName($this));
    }

    /**
     * @return void
     */
    public function testEndsWith(): void
    {
        self::assertTrue(Strings::endsWith('name', 'me'));
    }

    /**
     * @return void
     */
    public function testWebalize(): void
    {
        self::assertSame('nas-produkt', Strings::webalize('Náš produkt'));
    }

    /**
     * @throws ReflectionException
     */
    public function testGlibc(): void
    {
        $strings = new ReflectionClass(Strings::class);
        $method  = $strings->getMethod('glibc');
        $method->setAccessible(TRUE);

        self::assertEquals('Nas produkt', $method->invokeArgs(NULL, ['Naš produkt']));
    }

    /**
     * @throws ReflectionException
     */
    public function testIconv(): void
    {
        $strings = new ReflectionClass(Strings::class);
        $method  = $strings->getMethod('iconv');
        $method->setAccessible(TRUE);

        self::assertEquals('Nas produkt', $method->invokeArgs(NULL, ['Naš produkt']));
    }

    /**
     * @return mixed[]
     */
    public static function toCamelCaseDataProvider(): array
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

}
