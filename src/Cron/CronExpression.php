<?php declare(strict_types=1);

namespace Hanaboso\Utils\Cron;

use LogicException;

/**
 * Class CronExpression
 *
 * @package Hanaboso\Utils\Cron
 */
final class CronExpression
{

    /**
     * @var string
     */
    private string $minute;

    /**
     * @var string
     */
    private string $hour;

    /**
     * @var string
     */
    private string $dayOfMonth;

    /**
     * @var string
     */
    private string $month;

    /**
     * @var string
     */
    private string $dayOfWeek;

    /**
     * CronExpression constructor.
     *
     * @param string $minute
     * @param string $hour
     * @param string $dayOfMonth
     * @param string $month
     * @param string $dayOfWeek
     *
     * @throws LogicException
     */
    public function __construct(string $minute, string $hour, string $dayOfMonth, string $month, string $dayOfWeek)
    {
        $this->setMinute($minute);
        $this->setHour($hour);
        $this->setDayOfMonth($dayOfMonth);
        $this->setMonth($month);
        $this->setDayOfWeek($dayOfWeek);
    }

    /**
     * @return string
     */
    public function getMinute(): string
    {
        return $this->minute;
    }

    /**
     * @return string
     */
    public function getHour(): string
    {
        return $this->hour;
    }

    /**
     * @return string
     */
    public function getDayOfMonth(): string
    {
        return $this->dayOfMonth;
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * @return string
     */
    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    /**
     * ----------------------------------------------- HELPERS -------------------------------------
     */

    /**
     * @param string $minute
     */
    private function setMinute(string $minute): void
    {
        if (!preg_match('/^[*,\/\-0-9]+$/', $minute)) {
            throw new LogicException(sprintf('"%s" is not valid for minute part.', $minute));
        }

        $this->minute = $minute;
    }

    /**
     * @param string $hour
     */
    private function setHour(string $hour): void
    {
        if (!preg_match('/^[*,\/\-0-9]+$/', $hour)) {
            throw new LogicException(sprintf('"%s" is not valid for hour part.', $hour));
        }

        $this->hour = $hour;
    }

    /**
     * @param string $day
     */
    private function setDayOfMonth(string $day): void
    {
        if (!self::isValidDayOfMonth($day)) {
            throw new LogicException(sprintf('"%s" is not valid for dayOfMonth part.', $day));
        }

        $this->dayOfMonth = $day;
    }

    /**
     * @param string $month
     */
    private function setMonth(string $month): void
    {
        $month = str_ireplace(
            ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
            array_map(static fn($n) => (string) $n, range(1, 12)),
            $month,
        );

        if (!preg_match('/^[*,\/\-0-9]+$/', $month)) {
            throw new LogicException(sprintf('"%s" is not valid for month part.', $month));
        }

        $this->month = $month;
    }

    /**
     * @param string $day
     */
    private function setDayOfWeek(string $day): void
    {
        $day = str_ireplace(
            ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
            array_map(static fn($n) => (string) $n, range(0, 6)),
            $day,
        );

        if (!self::isValidDayOfWeek($day)) {
            throw new LogicException(sprintf('"%s" is not valid for hour part.', $day));
        }

        $this->dayOfWeek = $day;
    }

    /**
     * @param string $day
     *
     * @return bool
     */
    private static function isValidDayOfMonth(string $day): bool
    {
        // Allow wildcards
        if ($day === '*') {
            return TRUE;
        }

        // If you only contain numbers and are within 1-31
        if (preg_match('/^\d{1,2}$/', $day) && ($day >= 1 && $day <= 31)) {
            return TRUE;
        }

        // If you have a -, we will deal with each of your chunks
        if (str_contains($day, '-')) {
            // We cannot have a range within a list or vice versa
            if (str_contains($day, ',')) {
                return FALSE;
            }

            return self::isValidChunk($day, '-');
        }

        // If you have a comma, we will deal with each value
        if (str_contains($day, ',')) {
            return self::isValidChunk($day, ',');
        }

        // If you contain a /, we'll deal with it
        if (preg_match('/\//', $day)) {
            return self::isValidChunk($day, '/');
        }

        return FALSE;
    }

    /**
     * @param string           $day
     * @param non-empty-string $delimiter
     *
     * @return bool
     */
    private static function isValidChunk(string $day, string $delimiter): bool
    {
        $chunks = explode($delimiter, $day);

        return array_all($chunks, static fn($chunk) => self::isValidDayOfMonth($chunk));
    }

    /**
     * @param string $day
     *
     * @return bool
     */
    private static function isValidDayOfWeek(string $day): bool
    {
        return array_all(
            explode(',', $day),
            static fn(string $expr) => (bool) preg_match('/^(\*|[0-7](L?|#[1-5]))([\/,\-][0-7]+)*$/', $expr),
        );
    }

}
