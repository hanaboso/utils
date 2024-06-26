<?php declare(strict_types=1);

namespace UtilsTests\Unit\Date;

use DateTime;
use DateTimeImmutable;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeUtilsTest
 *
 * @package UtilsTests\Unit\Date
 */
#[CoversClass(DateTimeUtils::class)]
final class DateTimeUtilsTest extends TestCase
{

    /**
     * @throws DateTimeException
     */
    public function testGetUtcDateTime(): void
    {
        self::assertEquals(new DateTime('1.1.2020'), DateTimeUtils::getUtcDateTime('1.1.2020'));
    }

    /**
     * @throws DateTimeException
     */
    public function testGetUtcDateTimeError(): void
    {
        $this->expectException(DateTimeException::class);
        DateTimeUtils::getUtcDateTime('-');
    }

    /**
     * @return void
     */
    public function testGetUtcDateTimeFromTimeStamp(): void
    {
        self::assertEquals(new DateTime('1.1.2020'), DateTimeUtils::getUtcDateTimeFromTimeStamp(1_577_836_800));
    }

    /**
     * @throws DateTimeException
     */
    public function testGetUtcDateTimeImmutable(): void
    {
        self::assertEquals(new DateTimeImmutable('1.1.2020'), DateTimeUtils::getUtcDateTimeImmutable('1.1.2020'));
    }

    /**
     * @throws DateTimeException
     */
    public function testGetUtcDateTimeImmutableError(): void
    {
        $this->expectException(DateTimeException::class);
        DateTimeUtils::getUtcDateTimeImmutable('-');
    }

}
