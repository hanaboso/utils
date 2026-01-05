<?php declare(strict_types=1);

namespace UtilsTests\Unit\Traits;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * Class ControllerTraitTest
 *
 * @package UtilsTests\Unit\Traits
 */
final class ControllerTraitTest extends TestCase
{

    /**
     * @return void
     */
    public function testGetResponseArr(): void
    {
        self::assertEquals(
            '{"first":"1st","second":"2nd"}',
            new TestTraits()->getResponseForTest(['first' => '1st', 'second' => '2nd'])->getContent(),
        );
    }

    /**
     * @return void
     */
    public function testGetErrorResponse(): void
    {
        $test = new TestTraits();
        $test->setLogger(new NullLogger());
        self::assertEquals(
            '{"error_code":0,"message":"","status":"INTERNAL_SERVER_ERROR","type":"Exception"}',
            $test->getErrorResponseForTest(new Exception())->getContent(),
        );
    }

}
