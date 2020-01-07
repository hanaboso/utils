<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\DsnParser;
use PHPUnit\Framework\TestCase;

/**
 * Class DsnParserTest
 *
 * @package UtilsTests\Unit\String
 */
final class DsnParserTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::genericParser
     */
    public function testGenericParser(): void
    {
        $result = DsnParser::genericParser('http://guest:heslo@dev.company:1000/sss.qa');
        self::assertEquals(
            [
                'scheme' => 'http',
                'host'   => 'dev.company',
                'port'   => 1_000,
                'user'   => 'guest',
                'pass'   => 'heslo',
                'path'   => '/sss.qa',
            ],
            $result
        );
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::rabbitParser
     * @covers \Hanaboso\Utils\String\DsnParser::getQueryParamsArr
     */
    public function testRabbitParser(): void
    {
        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company:1000/sss.qa');
        self::assertEquals(
            [
                'user'     => 'guest',
                'password' => 'heslo',
                'host'     => 'dev.company',
                'port'     => 1_000,
                'vhost'    => 'sss.qa',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company/sss.qa');
        self::assertEquals(
            [
                'user'     => 'guest',
                'password' => 'heslo',
                'host'     => 'dev.company',
                'vhost'    => 'sss.qa',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company');
        self::assertEquals(
            [
                'user'     => 'guest',
                'password' => 'heslo',
                'host'     => 'dev.company',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                'user'               => 'guest',
                'password'           => 'heslo',
                'host'               => 'dev.company',
                'heartbeat'          => 10,
                'connection_timeout' => 10_000,
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://dev.company?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                'host'               => 'dev.company',
                'heartbeat'          => 10,
                'connection_timeout' => 10_000,
            ],
            $result
        );
    }

}