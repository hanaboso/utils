<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\DsnParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class DsnParserTest
 *
 * @package UtilsTests\Unit\String
 *
 * @covers  \Hanaboso\Utils\String\DsnParser
 */
final class DsnParserTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::genericParser
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithUsersCredentials
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
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithoutUsersCredentials
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithUsersCredentials
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

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company:1001?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                'user'               => 'guest',
                'password'           => 'heslo',
                'host'               => 'dev.company',
                'heartbeat'          => 10,
                'connection_timeout' => 10_000,
                'port'               => 1_001,
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://dev.company:8080/vhost?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                'host'               => 'dev.company',
                'heartbeat'          => 10,
                'connection_timeout' => 10_000,
                'port'               => 8_080,
                'vhost'              => 'vhost',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://rabbitmq:5672');
        self::assertEquals(
            [
                'host' => 'rabbitmq',
                'port' => 5_672,
            ],
            $result
        );
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::rabbitParser
     * @covers \Hanaboso\Utils\String\DsnParser::isValidRabbitDsn
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithoutUsersCredentials
     */
    public function testRabbitParserEnv(): void
    {
        $result = DsnParser::rabbitParser(
            'amqp://env_RABBITMQ_USER_000:env_RABBITMQ_PASS_111@env_RABBITMQ_HOST_222:env_RABBITMQ_PORT_333/env_RABBITMQ_VHOST_444'
        );
        self::assertEquals(
            [
                'user'     => 'env_RABBITMQ_USER_000',
                'password' => 'env_RABBITMQ_PASS_111',
                'host'     => 'env_RABBITMQ_HOST_222',
                'port'     => 'env_RABBITMQ_PORT_333',
                'vhost'    => 'env_RABBITMQ_VHOST_444',
            ],
            $result
        );
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::rabbitParser
     */
    public function testRabbitParserError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DsnParser::rabbitParser('uri://');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::isValidRabbitDsn
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithUsersCredentials
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithoutUsersCredentials
     */
    public function test1IsValidDsn(): void
    {
        $result = DsnParser::isValidRabbitDsn('amqp://guest:heslo@dev.company:1000/sss.qa');
        self::assertTrue($result);

        $result = DsnParser::isValidRabbitDsn('amqp://dev.company:8080/vhost?heartbeat=10&connection_timeout=10000');
        self::assertTrue($result);

        $result = DsnParser::isValidRabbitDsn('amqp://host:101/vhost');
        self::assertTrue($result);

        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://guest:heslo@dev.company/sss.qa');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::isValidRabbitDsn
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithUsersCredentials
     */
    public function test2IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://guest:heslo@dev.company');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::isValidRabbitDsn
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithUsersCredentials
     */
    public function test3IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://guest:heslo@dev.company?heartbeat=10&connection_timeout=10000');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::isValidRabbitDsn
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithUsersCredentials
     */
    public function test4IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://guest:heslo@:600');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::isValidRabbitDsn
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithoutUsersCredentials
     */
    public function test5IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://:600');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::isValidRabbitDsn
     * @covers \Hanaboso\Utils\String\DsnParser::regExWithoutUsersCredentials
     */
    public function test6IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://host');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::isValidRabbitDsn
     */
    public function test8IsValidDsn(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('uri://');
    }

    /**
     * @covers       \Hanaboso\Utils\String\DsnParser::parseRedisDsn
     *
     * @dataProvider parseRedisDsnProvider
     *
     * @param string  $dsn
     * @param mixed[] $exp
     */
    public function testParseRedisDsn(string $dsn, array $exp): void
    {
        $res = DsnParser::parseRedisDsn($dsn);
        self::assertEquals($exp, $res);
    }

    /**
     * @return mixed[]
     */
    public function parseRedisDsnProvider(): array
    {
        return [
            [
                'redis://localhost:6379/5',
                [
                    DsnParser::TLS      => FALSE,
                    DsnParser::DATABASE => 5,
                    DsnParser::HOST     => 'localhost',
                    DsnParser::PORT     => 6_379,
                ],
            ],
            [
                'redis://pw@[::1]:63790/10',
                [
                    DsnParser::TLS      => FALSE,
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::DATABASE => 10,
                    DsnParser::HOST     => '::1',
                    DsnParser::PORT     => 63_790,
                ],
            ],
            [
                'redis://%redis_pass%@%redis_host%:%redis_port%/%redis_db%',
                [
                    DsnParser::TLS      => FALSE,
                    DsnParser::PASSWORD => '%redis_pass%',
                    DsnParser::DATABASE => '%redis_db%',
                    DsnParser::HOST     => '%redis_host%',
                    DsnParser::PORT     => '%redis_port%',
                ],
            ],
            [
                'redis://p:pw@[::1]:63790/10',
                [
                    DsnParser::TLS      => FALSE,
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::DATABASE => 10,
                    DsnParser::HOST     => '::1',
                    DsnParser::PORT     => 63_790,
                ],
            ],
            [
                'redis://pw@/var/run/redis/redis-1.sock:63790/10',
                [
                    DsnParser::TLS      => FALSE,
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::DATABASE => 10,
                    DsnParser::SOCKET   => '/var/run/redis/redis-1.sock',
                ],
            ],
            [
                'redis://pw@/redis.sock/10?weight=8&alias=master',
                [
                    DsnParser::TLS      => FALSE,
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::WEIGHT   => 8,
                    DsnParser::ALIAS    => 'master',
                    DsnParser::DATABASE => 10,
                    DsnParser::SOCKET   => '/redis.sock',
                ],
            ],
            [
                'rediss://pw@/redis.sock/10?asd=',
                [
                    DsnParser::TLS      => TRUE,
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::DATABASE => 10,
                    DsnParser::SOCKET   => '/redis.sock',
                ],
            ],
        ];
    }

}
