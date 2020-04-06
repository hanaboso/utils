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
                'scheme'        => 'http',
                DsnParser::HOST => 'dev.company',
                DsnParser::PORT => 1_000,
                DsnParser::USER => 'guest',
                'pass'          => 'heslo',
                'path'          => '/sss.qa',
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
                DsnParser::USER     => 'guest',
                DsnParser::PASSWORD => 'heslo',
                DsnParser::HOST     => 'dev.company',
                DsnParser::PORT     => 1_000,
                DsnParser::VHOST    => 'sss.qa',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company:1001?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                DsnParser::USER      => 'guest',
                DsnParser::PASSWORD  => 'heslo',
                DsnParser::HOST      => 'dev.company',
                'heartbeat'          => 10,
                'connection_timeout' => 10_000,
                DsnParser::PORT      => 1_001,
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://dev.company:8080/vhost?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                DsnParser::HOST      => 'dev.company',
                'heartbeat'          => 10,
                'connection_timeout' => 10_000,
                DsnParser::PORT      => 8_080,
                DsnParser::VHOST     => DsnParser::VHOST,
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://rabbitmq:5672');
        self::assertEquals(
            [
                DsnParser::HOST => 'rabbitmq',
                DsnParser::PORT => 5_672,
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://dev-company-rabbit.cz:5672/dev-company');
        self::assertEquals(
            [
                DsnParser::HOST     => 'dev-company-rabbit.cz',
                DsnParser::PORT     => '5672',
                DsnParser::VHOST    => 'dev-company',
            ],
            $result
        );

        $result = DsnParser::rabbitParser('amqp://dev-company:pass@dev-company-rabbit.cz:5672/dev-company');
        self::assertEquals(
            [
                DsnParser::HOST     => 'dev-company-rabbit.cz',
                DsnParser::PORT     => '5672',
                DsnParser::USER     => 'dev-company',
                DsnParser::PASSWORD => 'pass',
                DsnParser::VHOST    => 'dev-company',
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
                DsnParser::USER     => 'env_RABBITMQ_USER_000',
                DsnParser::PASSWORD => 'env_RABBITMQ_PASS_111',
                DsnParser::HOST     => 'env_RABBITMQ_HOST_222',
                DsnParser::PORT     => 'env_RABBITMQ_PORT_333',
                DsnParser::VHOST    => 'env_RABBITMQ_VHOST_444',
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

    /**
     * @covers       \Hanaboso\Utils\String\DsnParser::parseElasticDsn
     *
     * @dataProvider parseElasticDsnProvider
     *
     * @param string  $dsn
     * @param mixed[] $exp
     */
    public function testParseElasticDsn($dsn, $exp): void
    {
        $res = DsnParser::parseElasticDsn($dsn);
        self::assertEquals($exp, $res);
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::parseElasticDsn
     */
    public function testParseElasticWrongDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::parseElasticDsn('elastic:localhost');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::parseElasticDsn
     */
    public function testParseElasticDsnHostsNotArray(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::parseElasticDsn('elasticsearch:?host');
    }

    /**
     * @covers \Hanaboso\Utils\String\DsnParser::parseElasticDsn
     */
    public function testParseElasticDsnErr(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::parseElasticDsn('elasticsearch://?[localhost]&host[localhost:9201]&host[127.0.0.1:9202]');
    }

    /**
     * @return mixed[]
     */
    public function parseElasticDsnProvider(): array
    {
        return [
            [
                'elasticsearch:localhost',
                [
                    DsnParser::SERVERS => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200]],
                ],
            ],
            [
                'elasticsearch://localhost',
                [
                    DsnParser::SERVERS => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200]],
                ],
            ],
            [
                'elasticsearch://example.com',
                [
                    DsnParser::SERVERS => [[DsnParser::HOST => 'example.com', DsnParser::PORT => 9_200]],
                ],
            ],
            [
                'elasticsearch://localhost:1234',
                [
                    DsnParser::SERVERS => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 1_234]],
                ],
            ],
            [
                'elasticsearch://foo:bar@localhost:1234',
                [
                    DsnParser::USERNAME => 'foo',
                    DsnParser::PASSWORD => 'bar',
                    DsnParser::SERVERS  => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 1_234]],
                ],
            ],
            [
                'elasticsearch:?host[localhost]&host[localhost:9201]&host[127.0.0.1:9202]',
                [
                    DsnParser::SERVERS => [
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200],
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_201],
                        [DsnParser::HOST => '127.0.0.1', DsnParser::PORT => 9_202],
                    ],
                ],
            ],
            [
                'elasticsearch:localhost:1234',
                [
                    DsnParser::SERVERS => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 1_234]],
                ],
            ],
            [
                'elasticsearch:foo:bar@?host[localhost:9201]',
                [
                    DsnParser::USERNAME => 'foo',
                    DsnParser::PASSWORD => 'bar',
                    DsnParser::SERVERS  => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 9_201]],
                ],
            ],
            [
                'elasticsearch://localhost:9201',
                [
                    DsnParser::SERVERS => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 9_201,]],
                ],
            ],
            [
                'elasticsearch://localhost/path?host[localhost]&host[localhost:9201]&host[127.0.0.1:9202]',
                [
                    DsnParser::SERVERS => [
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200],
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200],
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_201],
                        [DsnParser::HOST => '127.0.0.1', DsnParser::PORT => 9_202],
                    ],
                ],
            ],
            [
                'elasticsearch://localhost/path?host[localhost]&host[localhost:9201]&host[127.0.0.1:9202]&something[localhost]',
                [
                    DsnParser::SERVERS => [
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200],
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200],
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_201],
                        [DsnParser::HOST => '127.0.0.1', DsnParser::PORT => 9_202],
                    ],
                ],
            ],
            [
                'elasticsearch://localhost/path/1?host[localhost]&host[localhost:9201]&host[127.0.0.1:9202]&something[localhost]',
                [
                    DsnParser::SERVERS => [
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200],
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_200],
                        [DsnParser::HOST => 'localhost', DsnParser::PORT => 9_201],
                        [DsnParser::HOST => '127.0.0.1', DsnParser::PORT => 9_202],
                    ],
                ],
            ],
        ];
    }

}
