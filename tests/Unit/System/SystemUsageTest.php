<?php declare(strict_types=1);

namespace UtilsTests\Unit\System;

use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\Utils\System\SystemUsage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class SystemUsageTest
 *
 * @package UtilsTests\Unit\System
 */
#[CoversClass(SystemUsage::class)]
final class SystemUsageTest extends TestCase
{

    use PrivateTrait;

    /**
     * @return void
     */
    public function testGetCurrentTimestamp(): void
    {
        $ts = SystemUsage::getCurrentTimestamp();
        self::assertGreaterThan(0, $ts);

        $ts2 = SystemUsage::getCurrentTimestamp();
        self::assertGreaterThanOrEqual($ts, $ts2);
    }

    /**
     * @return void
     */
    public function testGetCpuTimes(): void
    {
        $before = SystemUsage::getCpuTimes();
        self::assertArrayHasKey(SystemUsage::CPU_TIME_USER, $before);
        self::assertArrayHasKey(SystemUsage::CPU_TIME_KERNEL, $before);
        self::assertArrayHasKey(SystemUsage::CPU_START_TIME, $before);
        self::assertGreaterThan(0, $before[SystemUsage::CPU_TIME_USER]);
        self::assertGreaterThanOrEqual(0, $before[SystemUsage::CPU_TIME_KERNEL]);
        self::assertGreaterThan(0, $before[SystemUsage::CPU_START_TIME]);

        $cpuUsageBefore = SystemUsage::getCpuUsage();
        self::assertGreaterThan(0, $cpuUsageBefore);
    }

}
