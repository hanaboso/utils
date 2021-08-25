<?php declare(strict_types=1);

namespace Hanaboso\Utils\Date;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Hanaboso\Utils\Exception\DateTimeException;
use Throwable;

/**
 * Class DateTimeUtils
 *
 * @package Hanaboso\Utils\Date
 */
class DateTimeUtils
{

    public const DATE_TIME     = 'Y-m-d H:i:s';
    public const DATE_TIME_UTC = 'Y-m-d\TH:i:s\Z';
    public const DATE          = 'Y-m-d';
    public const MYSQL_DATE    = '%Y-%m-%d';
    public const DATE_TIME_GO  = 'Y-m-d\TH:i:s.u\Z';

    /**
     * @param string $dateTime
     *
     * @return DateTime
     * @throws DateTimeException
     */
    public static function getUtcDateTime(string $dateTime = 'NOW'): DateTime
    {
        try {
            return (new DateTime($dateTime))->setTimezone(new DateTimeZone('UTC'));
        } catch (Throwable $t) {
            throw new DateTimeException($t->getMessage(), $t->getCode(), $t);
        }
    }

    /**
     * @param int $timeStamp
     *
     * @return DateTime
     */
    public static function getUtcDateTimeFromTimeStamp(int $timeStamp = 0): DateTime
    {
        /** @var DateTime $dateTime */
        $dateTime = DateTime::createFromFormat('U', (string) $timeStamp, new DateTimeZone('UTC'));

        return $dateTime;
    }

    /**
     * @param string $dateTime
     *
     * @return DateTimeImmutable
     * @throws DateTimeException
     */
    public static function getUtcDateTimeImmutable(string $dateTime = 'NOW'): DateTimeImmutable
    {
        try {
            return (new DateTimeImmutable($dateTime))->setTimezone(new DateTimeZone('UTC'));
        } catch (Throwable $t) {
            throw new DateTimeException($t->getMessage(), $t->getCode(), $t);
        }
    }

}
