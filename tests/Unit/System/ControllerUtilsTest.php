<?php declare(strict_types=1);

namespace UtilsTests\Unit\System;

use Exception;
use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\System\ControllerUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class ControllerUtilsTest
 *
 * @package UtilsTests\Unit\System
 */
final class ControllerUtilsTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\System\ControllerUtils::createExceptionData
     */
    public function testCreateExceptionData(): void
    {
        self::assertEquals(
            '{"status":"INTERNAL_SERVER_ERROR","error_code":400,"type":"Exception","message":"Ups, something went wrong"}',
            ControllerUtils::createExceptionData(new Exception('Ups, something went wrong', 400))
        );
    }

    /**
     * @covers \Hanaboso\Utils\System\ControllerUtils::createHeaders
     */
    public function testCreateHeader(): void
    {
        $headers                     = ControllerUtils::createHeaders(
            [
                'Accept'          => 'text/html',
                'Accept-Language' => 'en-us',
                'content-type'    => 'text/html',
            ],
            new Exception('Ups, something went wrong', 400)
        );
        $headers['pf-result-detail'] = 'detail';
        self::assertEquals(
            [
                'pf-result-code'    => 400,
                'pf-result-message' => 'Ups, something went wrong',
                'pf-result-detail'  => 'detail',
                'content-type'      => 'text/html',
            ],
            $headers
        );
    }

    /**
     * @covers \Hanaboso\Utils\System\ControllerUtils::checkParameters
     * @throws PipesFrameworkException
     */
    public function testCheckParametersErr(): void
    {
        $this->expectException(PipesFrameworkException::class);
        ControllerUtils::checkParameters(['name', 'detail'], ['name' => 'name']);
    }

    /**
     * @covers \Hanaboso\Utils\System\ControllerUtils::checkParameters
     * @throws PipesFrameworkException
     */
    public function testCheckParameters(): void
    {
        ControllerUtils::checkParameters(['name', 'detail'], ['name' => 'name', 'detail' => 'detail']);
        self::assertTrue(TRUE);
    }

}
