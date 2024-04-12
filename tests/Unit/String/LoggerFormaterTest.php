<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Exception;
use Hanaboso\Utils\String\LoggerFormater;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class LoggerFormaterTest
 *
 * @package UtilsTests\Unit\String
 */
#[CoversClass(LoggerFormater::class)]
final class LoggerFormaterTest extends TestCase
{

    /**
     * @return void
     */
    public function testHeadersToString(): void
    {
        self::assertSame(
            'content-type=[application/json, application/js], pf_token=123',
            LoggerFormater::headersToString(
                [
                    'content-type' => ['application/json', 'application/js'], 'pf_token' => '123',
                ],
            ),
        );
    }

    /**
     * @return void
     */
    public function testRequestToString(): void
    {
        self::assertSame(
            'Request: Method: GET, Uri: http://localhost, Headers: content-type=application/json, Body: "{"data":[]}"',
            LoggerFormater::requestToString(
                'get',
                'http://localhost',
                ['content-type' => 'application/json'],
                '{"data":[]}',
            ),
        );
    }

    /**
     * @return void
     */
    public function testResponseToString(): void
    {
        self::assertSame(
            'Response: Status Code: 400, Reason Phrase: Bad Request, Headers: content-type=application/json, Body: "{"data":[]}"',
            LoggerFormater::responseToString(
                400,
                'Bad Request',
                ['content-type' => 'application/json'],
                '{"data":[]}',
            ),
        );
    }

    /**
     * @return void
     */
    public function testGetContextForLogger(): void
    {
        self::assertEquals(
            'Ups, something went wrong',
            LoggerFormater::getContextForLogger(new Exception('Ups, something went wrong', 400))['message'],
        );
    }

    /**
     * @return void
     */
    public function testGetContextForLoggerNull(): void
    {
        self::assertEmpty(LoggerFormater::getContextForLogger());
    }

}
