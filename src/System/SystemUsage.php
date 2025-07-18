<?php declare(strict_types=1);

namespace Hanaboso\Utils\System;

use Exception;
use Hanaboso\Utils\File\File;

/**
 * Class SystemUsage
 *
 * @package Hanaboso\Utils\System
 */
final class SystemUsage
{

    public const string CPU_TIME_USER   = 'cpu_user_code_time';
    public const string CPU_TIME_KERNEL = 'cpu_kernel_code_time';
    public const string CPU_START_TIME  = 'cpu_start_time';

    private const int HERTZ = 100;

    private const string FILE_PROC_UPTIME = '/proc/uptime';
    private const string FILE_PROC_STAT   = '/proc/%s/stat';

    /**
     * Returns current CPU usage in percents
     *
     * Calculation made according to: https://stackoverflow.com/a/16736599/7200406
     *
     * @return float
     */
    public static function getCpuUsage(): float
    {
        try {
            $upTimeContent = File::getContent(self::FILE_PROC_UPTIME);
            $upTime        = (float) explode(' ', $upTimeContent)[0];

            $cpuTimes  = self::getCpuTimes();
            $totalTime = $cpuTimes[self::CPU_TIME_USER] + $cpuTimes[self::CPU_TIME_KERNEL];
            $seconds   = $upTime - ($cpuTimes[self::CPU_START_TIME] / self::HERTZ);
            $f         = $totalTime / self::HERTZ / $seconds;

            return 100 * $f;
        } catch (Exception) {
            return 0;
        }
    }

    /**
     * @return mixed[]
     */
    public static function getCpuTimes(): array
    {
        try {
            $pid         = getmypid();
            $statFile    = sprintf(self::FILE_PROC_STAT, $pid);
            $statContent = File::getContent($statFile);
            $stats       = explode(' ', $statContent);

            $uTime     = (float) $stats[13];
            $sTime     = (float) $stats[14];
            $cuTime    = (float) $stats[15];
            $csTime    = (float) $stats[16];
            $startTime = $stats[21];

            return [
                self::CPU_START_TIME  => $startTime,
                self::CPU_TIME_KERNEL => $sTime + $csTime,
                self::CPU_TIME_USER   => $uTime + $cuTime,
            ];
        } catch (Exception) {
            return [
                self::CPU_START_TIME  => 0,
                self::CPU_TIME_KERNEL => 0,
                self::CPU_TIME_USER   => 0,
            ];
        }
    }

    /**
     * @return int
     */
    public static function getCurrentTimestamp(): int
    {
        return (int) round(microtime(TRUE) * 1_000);
    }

}
