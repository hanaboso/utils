<?php declare(strict_types=1);

namespace Hanaboso\Utils\Cron;

use LogicException;

/**
 * Class CronParser
 *
 * @package Hanaboso\Utils\Cron
 */
final class CronParser
{

    /**
     * @var string[]
     */
    private static array $mappings = [
        '@yearly'   => '0 0 1 1 *',
        '@annually' => '0 0 1 1 *',
        '@monthly'  => '0 0 1 * *',
        '@weekly'   => '0 0 * * 0',
        '@daily'    => '0 0 * * *',
        '@hourly'   => '0 * * * *',
    ];

    /**
     * @param string $expression
     *
     * @return bool
     */
    public static function isValidExpression(string $expression): bool
    {
        try {
            self::parse($expression);
        } catch (LogicException) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @param string $expression
     *
     * @return CronExpression
     */
    public static function parse(string $expression): CronExpression
    {
        if (isset(self::$mappings[$expression])) {
            $expression = self::$mappings[$expression];
        }

        $parts = preg_split('/\s/', $expression, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $count = count($parts);
        if ($count < 5 || $count > 5) {
            throw new LogicException(sprintf('"%s" is not a valid CRON expression', $expression));
        }

        return new CronExpression(...$parts);
    }

}
