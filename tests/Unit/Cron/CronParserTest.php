<?php declare(strict_types=1);

namespace UtilsTests\Unit\Cron;

use Hanaboso\Utils\Cron\CronParser;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use UtilsTests\KernelTestCaseAbstract;

/**
 * Class CronParserTest
 *
 * @package UtilsTests\Unit\Cron
 */
final class CronParserTest extends KernelTestCaseAbstract
{

    /**
     * @return void
     */
    public function testIsValid(): void
    {
        self::assertTrue(CronParser::isValidExpression('@annually'));
        self::assertFalse(CronParser::isValidExpression('* *'));
    }

    /**
     * @return void
     */
    public function testMapping(): void
    {
        $c = CronParser::parse('@weekly');

        self::assertSame('0', $c->getMinute());
        self::assertSame('0', $c->getHour());
        self::assertSame('*', $c->getDayOfMonth());
        self::assertSame('*', $c->getMonth());
        self::assertSame('0', $c->getDayOfWeek());
    }

    /**
     * @return void
     */
    public function testGetters(): void
    {
        $c = CronParser::parse('5 4 15 JUN MON');

        self::assertSame('5', $c->getMinute());
        self::assertSame('4', $c->getHour());
        self::assertSame('15', $c->getDayOfMonth());
        self::assertSame('6', $c->getMonth());
        self::assertSame('1', $c->getDayOfWeek());
    }

    /**
     * @param string $expr
     * @param bool   $success
     */
    #[DataProvider('cronDataProvider')]
    public function testParse(string $expr, bool $success): void
    {
        if (!$success) {
            self::expectException(LogicException::class);
        }

        CronParser::parse($expr);
        self::assertFake();
    }

    /**
     * @return mixed[]
     */
    public static function cronDataProvider(): array
    {
        return [
            // valid values
            ['* * * * *', TRUE],
            ['* * 01 * *', TRUE],
            ['* * 01-05 * *', TRUE],
            ['* * 01,05 * *', TRUE],
            ['* * 01/05 * *', TRUE],
            // invalid values
            ['* * * *', FALSE],
            ['* * * * * *', FALSE],
            ['A * * * *', FALSE],
            ['* A * * *', FALSE],
            ['* * A * *', FALSE],
            ['* * * A *', FALSE],
            ['* * * * A', FALSE],
            ['* * 01-05,07 * *', FALSE],
            ['* * 01,05-07 * *', FALSE],
            ['* * 01-UND * *', FALSE],
        ];
    }

}
