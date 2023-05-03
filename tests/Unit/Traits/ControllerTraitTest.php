<?php declare(strict_types=1);

namespace UtilsTests\Unit\Traits;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Log\Logger;

/**
 * Class ControllerTraitTest
 *
 * @package UtilsTests\Unit\Traits
 */
final class ControllerTraitTest extends TestCase
{

    /**
     * @covers \UtilsTests\Unit\Traits\TestTraits::getResponse
     */
    public function testGetResponseArr(): void
    {
        self::assertEquals(
            '{"first":"1st","second":"2nd"}',
            (new TestTraits())->getResponseForTest(['first' => '1st', 'second' => '2nd'])->getContent(),
        );
    }

    /**
     * @covers \UtilsTests\Unit\Traits\TestTraits::getErrorResponse
     * @covers \UtilsTests\Unit\Traits\TestTraits::setLogger
     */
    public function testGetErrorResponse(): void
    {
        $test = new TestTraits();
        $test->setLogger(new Logger());
        self::assertEquals(
            '{"error_code":0,"message":"","status":"INTERNAL_SERVER_ERROR","type":"Exception"}',
            $test->getErrorResponseForTest(new Exception())->getContent(),
        );
    }

}
