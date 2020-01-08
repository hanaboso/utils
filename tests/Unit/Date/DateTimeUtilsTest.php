<?php declare(strict_types=1);

namespace UtilsTests\Unit\Date;

use DateTime;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeUtilsTest
 *
 * @package UtilsTests\Unit\Date
 */
final class DateTimeUtilsTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\Date\DateTimeUtils::getUtcDateTime
     *
     * @throws DateTimeException
     */
    public function testGetUtcDateTime(): void
    {
        self::assertEquals(new DateTime('1.1.2020'), DateTimeUtils::getUtcDateTime('1.1.2020'));
    }

    /**
     * @covers \Hanaboso\Utils\Date\DateTimeUtils::getUtcDateTime
     *
     * @throws DateTimeException
     */
    public function testGetUtcDateTimeError(): void
    {
        $this->expectException(DateTimeException::class);
        DateTimeUtils::getUtcDateTime('-');
    }

    /**
     * @covers \Hanaboso\Utils\Date\DateTimeUtils::getUtcDateTimeFromTimeStamp
     */
    public function testGetUtcDateTimeFromTimeStamp(): void
    {
        self::assertEquals(new DateTime('1.1.2020'), DateTimeUtils::getUtcDateTimeFromTimeStamp(1_577_836_800));
    }

}