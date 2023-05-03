<?php declare(strict_types=1);

namespace UtilsTests\Unit\Cron;

use Hanaboso\Utils\Cron\CronParser;
use LogicException;
use UtilsTests\KernelTestCaseAbstract;

/**
 * Class CronParserTest
 *
 * @package UtilsTests\Unit\Cron
 */
final class CronParserTest extends KernelTestCaseAbstract
{

    /**
     *
     */
    public function testIsValid(): void
    {
        self::assertTrue(CronParser::isValidExpression('@annually'));
        self::assertFalse(CronParser::isValidExpression('* *'));
    }

    /**
     *
     */
    public function testMapping(): void
    {
        $c = CronParser::parse('@weekly');

        self::assertEquals('0', $c->getMinute());
        self::assertEquals('0', $c->getHour());
        self::assertEquals('*', $c->getDayOfMonth());
        self::assertEquals('*', $c->getMonth());
        self::assertEquals('0', $c->getDayOfWeek());
    }

    /**
     *
     */
    public function testGetters(): void
    {
        $c = CronParser::parse('5 4 15 JUN MON');

        self::assertEquals('5', $c->getMinute());
        self::assertEquals('4', $c->getHour());
        self::assertEquals('15', $c->getDayOfMonth());
        self::assertEquals('6', $c->getMonth());
        self::assertEquals('1', $c->getDayOfWeek());
    }

    /**
     * @dataProvider cronDataProvider
     *
     * @param string $expr
     * @param bool   $success
     */
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
