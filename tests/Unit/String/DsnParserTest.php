<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Hanaboso\Utils\String\DsnParser;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class DsnParserTest
 *
 * @package UtilsTests\Unit\String
 */
#[CoversClass(DsnParser::class)]
final class DsnParserTest extends TestCase
{

    /**
     * @return void
     */
    public function testGenericParser(): void
    {
        $result = DsnParser::genericParser('https://guest:heslo@dev.company:1000/sss.qa');
        self::assertEquals(
            [
                'pass'          => 'heslo',
                'path'          => '/sss.qa',
                'scheme'        => 'https',
                DsnParser::HOST => 'dev.company',
                DsnParser::PORT => 1_000,
                DsnParser::USER => 'guest',
            ],
            $result,
        );
    }

    /**
     * @return void
     */
    public function testRabbitParser(): void
    {
        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company:1000/sss.qa');
        self::assertEquals(
            [
                DsnParser::HOST     => 'dev.company',
                DsnParser::PASSWORD => 'heslo',
                DsnParser::PORT     => 1_000,
                DsnParser::USER     => 'guest',
                DsnParser::VHOST    => 'sss.qa',
            ],
            $result,
        );

        $result = DsnParser::rabbitParser('amqp://guest:heslo@dev.company:1001?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                'connection_timeout' => 10_000,
                'heartbeat'          => 10,
                DsnParser::HOST      => 'dev.company',
                DsnParser::PASSWORD  => 'heslo',
                DsnParser::PORT      => 1_001,
                DsnParser::USER      => 'guest',
            ],
            $result,
        );

        $result = DsnParser::rabbitParser('amqp://dev.company:8080/vhost?heartbeat=10&connection_timeout=10000');
        self::assertEquals(
            [
                'connection_timeout' => 10_000,
                'heartbeat'          => 10,
                DsnParser::HOST      => 'dev.company',
                DsnParser::PORT      => 8_080,
                DsnParser::VHOST     => DsnParser::VHOST,
            ],
            $result,
        );

        $result = DsnParser::rabbitParser('amqp://rabbitmq:5672');
        self::assertEquals(
            [
                DsnParser::HOST => 'rabbitmq',
                DsnParser::PORT => 5_672,
            ],
            $result,
        );

        $result = DsnParser::rabbitParser('amqp://dev-company-rabbit.cz:5672/dev-company');
        self::assertEquals(
            [
                DsnParser::HOST     => 'dev-company-rabbit.cz',
                DsnParser::PORT     => '5672',
                DsnParser::VHOST    => 'dev-company',
            ],
            $result,
        );

        $result = DsnParser::rabbitParser('amqp://dev-company:pass@dev-company-rabbit.cz:5672/dev-company');
        self::assertEquals(
            [
                DsnParser::HOST     => 'dev-company-rabbit.cz',
                DsnParser::PASSWORD => 'pass',
                DsnParser::PORT     => '5672',
                DsnParser::USER     => 'dev-company',
                DsnParser::VHOST    => 'dev-company',
            ],
            $result,
        );
    }

    /**
     * @return void
     */
    public function testRabbitParserEnv(): void
    {
        $result = DsnParser::rabbitParser(
            'amqp://env_RABBITMQ_USER_000:env_RABBITMQ_PASS_111@env_RABBITMQ_HOST_222:env_RABBITMQ_PORT_333/env_RABBITMQ_VHOST_444',
        );
        self::assertEquals(
            [
                DsnParser::HOST     => 'env_RABBITMQ_HOST_222',
                DsnParser::PASSWORD => 'env_RABBITMQ_PASS_111',
                DsnParser::PORT     => 'env_RABBITMQ_PORT_333',
                DsnParser::USER     => 'env_RABBITMQ_USER_000',
                DsnParser::VHOST    => 'env_RABBITMQ_VHOST_444',
            ],
            $result,
        );
    }

    /**
     * @return void
     */
    public function testRabbitParserError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DsnParser::rabbitParser('uri://');
    }

    /**
     * @return void
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
     * @return void
     */
    public function test2IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://guest:heslo@dev.company');
    }

    /**
     * @return void
     */
    public function test3IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://guest:heslo@dev.company?heartbeat=10&connection_timeout=10000');
    }

    /**
     * @return void
     */
    public function test4IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://guest:heslo@:600');
    }

    /**
     * @return void
     */
    public function test5IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://:600');
    }

    /**
     * @return void
     */
    public function test6IsValidDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('amqp://host');
    }

    /**
     * @return void
     */
    public function test8IsValidDsn(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DsnParser::isValidRabbitDsn('uri://');
    }

    /**
     * @param string  $dsn
     * @param mixed[] $exp
     */
    #[DataProvider('parseRedisDsnProvider')]
    public function testParseRedisDsn(string $dsn, array $exp): void
    {
        $res = DsnParser::parseRedisDsn($dsn);
        self::assertEquals($exp, $res);
    }

    /**
     * @param string  $dsn
     * @param mixed[] $exp
     */
    #[DataProvider('parseElasticDsnProvider')]
    public function testParseElasticDsn(string $dsn, array $exp): void
    {
        $res = DsnParser::parseElasticDsn($dsn);
        self::assertEquals($exp, $res);
    }

    /**
     * @return void
     */
    public function testParseElasticWrongDsn(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::parseElasticDsn('elastic:localhost');
    }

    /**
     * @return void
     */
    public function testParseElasticDsnHostsNotArray(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::parseElasticDsn('elasticsearch:?host');
    }

    /**
     * @return void
     */
    public function testParseElasticDsnErr(): void
    {
        self::expectException(InvalidArgumentException::class);
        DsnParser::parseElasticDsn('elasticsearch://?[localhost]&host[localhost:9201]&host[127.0.0.1:9202]');
    }

    /**
     * @return mixed[]
     */
    public static function parseRedisDsnProvider(): array
    {
        return [
            [
                'redis://localhost:6379/5',
                [
                    DsnParser::DATABASE => 5,
                    DsnParser::HOST     => 'localhost',
                    DsnParser::PORT     => 6_379,
                    DsnParser::TLS      => FALSE,
                ],
            ],
            [
                'redis://pw@[::1]:63790/10',
                [
                    DsnParser::DATABASE => 10,
                    DsnParser::HOST     => '::1',
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::PORT     => 63_790,
                    DsnParser::TLS      => FALSE,
                ],
            ],
            [
                'redis://%redis_pass%@%redis_host%:%redis_port%/%redis_db%',
                [
                    DsnParser::DATABASE => '%redis_db%',
                    DsnParser::HOST     => '%redis_host%',
                    DsnParser::PASSWORD => '%redis_pass%',
                    DsnParser::PORT     => '%redis_port%',
                    DsnParser::TLS      => FALSE,
                ],
            ],
            [
                'redis://p:pw@[::1]:63790/10',
                [
                    DsnParser::DATABASE => 10,
                    DsnParser::HOST     => '::1',
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::PORT     => 63_790,
                    DsnParser::TLS      => FALSE,
                ],
            ],
            [
                'redis://pw@/var/run/redis/redis-1.sock:63790/10',
                [
                    DsnParser::DATABASE => 10,
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::SOCKET   => '/var/run/redis/redis-1.sock',
                    DsnParser::TLS      => FALSE,
                ],
            ],
            [
                'redis://pw@/redis.sock/10?weight=8&alias=master',
                [
                    DsnParser::ALIAS    => 'master',
                    DsnParser::DATABASE => 10,
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::SOCKET   => '/redis.sock',
                    DsnParser::TLS      => FALSE,
                    DsnParser::WEIGHT   => 8,
                ],
            ],
            [
                'rediss://pw@/redis.sock/10?asd=',
                [
                    DsnParser::DATABASE => 10,
                    DsnParser::PASSWORD => 'pw',
                    DsnParser::SOCKET   => '/redis.sock',
                    DsnParser::TLS      => TRUE,
                ],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function parseElasticDsnProvider(): array
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
                    DsnParser::PASSWORD => 'bar',
                    DsnParser::SERVERS  => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 1_234]],
                    DsnParser::USERNAME => 'foo',
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
                    DsnParser::PASSWORD => 'bar',
                    DsnParser::SERVERS  => [[DsnParser::HOST => 'localhost', DsnParser::PORT => 9_201]],
                    DsnParser::USERNAME => 'foo',
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
