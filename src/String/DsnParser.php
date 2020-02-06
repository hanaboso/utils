<?php declare(strict_types=1);

namespace Hanaboso\Utils\String;

use InvalidArgumentException;

/**
 * Class DsnParser
 *
 * @package Hanaboso\Utils\String
 */
final class DsnParser
{

    public const USER     = 'user';
    public const HOST     = 'host';
    public const VHOST    = 'vhost';
    public const PORT     = 'port';
    public const PASSWORD = 'password';
    public const DATABASE = 'database';
    public const TLS      = 'tls';
    public const SOCKET   = 'socket';
    public const WEIGHT   = 'weight';
    public const ALIAS    = 'alias';
    public const QUERY    = 'query';
    public const PATH     = 'path';
    public const SERVERS  = 'servers';
    public const USERNAME = 'username';

    /**
     * @param string $dsn
     *
     * @return mixed
     */
    public static function genericParser(string $dsn)
    {
        return parse_url($dsn);
    }

    /**
     * @param string $dsn
     *
     * @return mixed[]
     */
    public static function rabbitParser(string $dsn): array
    {
        self::isValidRabbitDsn($dsn);

        $queryArr = [];
        if (strpos($dsn, '@')) {
            $parsedUrl = self::regExWithUsersCredentials($dsn);

            if (isset($parsedUrl[7])) {
                $queryArr = self::getQueryParamsArr($parsedUrl[7]);
            }

            $result = [
                self::USER     => $parsedUrl[1],
                self::PASSWORD => $parsedUrl[2],
                self::HOST     => $parsedUrl[3],
            ];

            if (!empty($queryArr)) {
                $result = array_merge($result, $queryArr);
            }

            if ((isset($parsedUrl[6]) && !empty($parsedUrl[6])) || (isset($parsedUrl[4]) && !empty($parsedUrl[4]))) {
                $result[self::PORT] = isset($parsedUrl[6]) && !empty($parsedUrl[6]) ? $parsedUrl[6] : $parsedUrl[4];
            }

            if (isset($parsedUrl[5]) && !empty($parsedUrl[5])) {
                $result[self::VHOST] = $parsedUrl[5];
            }

            return $result;
        } else {
            $parsedUrl = self::regExWithoutUsersCredentials($dsn);

            if (isset($parsedUrl[5])) {
                $queryArr = self::getQueryParamsArr($parsedUrl[5]);
            }

            $result = [
                self::HOST => $parsedUrl[1],
            ];

            if (!empty($queryArr)) {
                $result = array_merge($result, $queryArr);
            }

            if ((isset($parsedUrl[2]) && !empty($parsedUrl[2])) || (isset($parsedUrl[4]) && !empty($parsedUrl[4]))) {
                $result[self::PORT] = isset($parsedUrl[2]) && !empty($parsedUrl[2]) ? $parsedUrl[2] : $parsedUrl[4];
            }

            if ((isset($parsedUrl[3]) && !empty($parsedUrl[3]))) {
                $result[self::VHOST] = $parsedUrl[3];
            }

            return $result;
        }
    }

    /**
     * @param string $dsn
     *
     * @return bool
     */
    public static function isValidRabbitDsn(string $dsn): bool
    {
        if (strpos($dsn, 'amqp://') === FALSE) {
            throw new InvalidArgumentException(sprintf('The given AMQP DSN "%s" is invalid.', $dsn));
        }

        if (strpos($dsn, '@')) {
            $parsedUrl = self::regExWithUsersCredentials($dsn);

            if (empty($parsedUrl[3])) {
                throw new InvalidArgumentException('Host was not provided.');
            }

            if (empty($parsedUrl[4]) && empty($parsedUrl[6])) {
                throw new InvalidArgumentException('Port was not provided.');
            }
        } else {
            $parsedUrl = self::regExWithoutUsersCredentials($dsn);

            if (empty($parsedUrl[1])) {
                throw new InvalidArgumentException('Host was not provided');
            }

            if (empty($parsedUrl[4]) && empty($parsedUrl[3])) {
                throw new InvalidArgumentException('Port was not provided.');
            }
        }

        return TRUE;
    }

    /**
     * @param string $dsn
     *
     * @return mixed[]
     */
    public static function parseRedisDsn(string $dsn): array
    {
        $result = [
            self::TLS => strpos($dsn, 'rediss://') === 0,
        ];

        $dsn = preg_replace('#rediss?://#', '', $dsn) ?: '';
        $pos = strrpos($dsn, '@');
        if ($pos !== FALSE) {
            $password = substr($dsn, 0, $pos);

            if (strstr($password, ':')) {
                [, $password] = explode(':', $password, 2);
            }

            $result[self::PASSWORD] = urldecode($password);

            $dsn = substr($dsn, $pos + 1) ?: '';
        }

        $callback = static function (array $args) use (&$result): void {
            self::parseRedisParameters($args, $result);
        };

        $dsn = preg_replace_callback('/\?(.*)$/', $callback, $dsn) ?: '';
        if (preg_match('#^(.*)/(\d+|%[^%]+%)$#', $dsn, $matches)) {
            // parse database
            $result[self::DATABASE] = is_numeric($matches[2]) ? (int) $matches[2] : $matches[2];
            $dsn                    = $matches[1];
        }
        if (preg_match('#^([^:]+)(:(\d+|%[^%]+%))?$#', $dsn, $matches)) {
            if (!empty($matches[1])) {
                // parse host/ip or socket
                if ($matches[1][0] === '/') {
                    $result[self::SOCKET] = $matches[1];
                } else {
                    $result[self::HOST] = $matches[1];
                }
            }
            if (($result[self::SOCKET] ?? NULL) === NULL && !empty($matches[3])) {
                // parse port
                $result[self::PORT] = is_numeric($matches[3]) ? (int) $matches[3] : $matches[3];
            }
        } else {
            if (preg_match(
                '#^\[([^\]]+)](:(\d+))?$#',
                $dsn,
                $matches
            )) { // parse enclosed IPv6 address and optional port
                if (!empty($matches[1])) {
                    $result[self::HOST] = $matches[1];
                }
                if (!empty($matches[3])) {
                    $result[self::PORT] = (int) $matches[3];
                }
            }
        }

        return $result;
    }

    /**
     * @param string $dsn
     *
     * @return mixed[]
     */
    public static function parseElasticDsn(string $dsn): array
    {
        $servers  = [];
        $query    = [];
        $hosts    = [];
        $username = NULL;
        $password = NULL;

        if (mb_strpos($dsn, 'elasticsearch:') !== 0) {
            throw new InvalidArgumentException(
                sprintf('Invalid Elasticsearch DSN: %s does not start with "elasticsearch:"', $dsn)
            );
        }
        $params = preg_replace_callback(
            '#^elasticsearch:(//)?(?:([^@]*+)@)?#',
            static function ($m) use (&$username, &$password): string {
                if (!empty($m[2])) {
                    [$username, $password] = explode(':', $m[2], 2) + [1 => NULL];
                }

                return sprintf('file:%s', $m[1] ?? '');
            },
            $dsn
        );
        $params = (array) parse_url((string) $params);

        if (isset($params[self::QUERY])) {
            parse_str($params[self::QUERY], $query);

            if (isset($query[self::HOST])) {
                $hosts = $query[self::HOST];
                if (!is_array($hosts)) {
                    throw new InvalidArgumentException(sprintf('Invalid Elasticsearch DSN: %s', $dsn));
                }
                foreach (array_keys($hosts) as $host) {
                    $port = mb_strrpos($host, ':');
                    if ($port === FALSE) {
                        $hosts[$host] = [self::HOST => $host, self::PORT => 9_200];
                    } else {
                        $hosts[$host] = [
                            self::HOST => mb_substr($host, 0, $port), self::PORT => (int) mb_substr($host, 1 + $port),
                        ];
                    }
                }
                $hosts = array_values($hosts);
                unset($query[self::HOST]);
            }
            if (!isset($params[self::HOST], $params[self::PATH])) {

                $servers = array_merge($servers, $hosts);

                $config = [self::SERVERS => $servers];
                if ($username !== NULL) {
                    $config[self::USERNAME] = $username;
                }
                if ($password !== NULL) {
                    $config[self::PASSWORD] = $password;
                }

                return $config;
            }
        }

        if (!isset($params[self::HOST]) && !isset($params[self::PATH])) {
            throw new InvalidArgumentException(sprintf('Invalid Elasticsearch DSN: %s', $dsn));
        }

        if (isset($params[self::PATH]) && preg_match('#/(\d+)$#', $params[self::PATH], $m)) {
            $params[self::PATH] = mb_substr($params['path'], 0, -mb_strlen($m[0]));
        }

        if (isset($params[self::PATH]) && preg_match('#:(\d+)$#', $params[self::PATH], $m)) {
            $params[self::HOST] = mb_substr($params[self::PATH], 0, -mb_strlen($m[0]));
            $params[self::PORT] = $m[1];
            unset($params[self::PATH]);
        }

        $params += [
            self::HOST => $params[self::HOST] ?? $params[self::PATH] ?? NULL,
            self::PORT => !isset($params[self::PORT]) ? 9_200 : NULL,
        ];
        if ($query) {
            $params += $query;
        }

        $servers[] = [self::HOST => $params[self::HOST], self::PORT => $params[self::PORT]];

        if ($hosts) {
            $servers = array_merge($servers, $hosts);
        }

        $config = [self::SERVERS => $servers];
        if ($username !== NULL) {
            $config[self::USERNAME] = $username;
        }
        if ($password !== NULL) {
            $config[self::PASSWORD] = $password;
        }

        return $config;
    }

    /**
     * @param string $queryString
     *
     * @return mixed[]
     */
    private static function getQueryParamsArr(string $queryString): array
    {
        $queryArr   = [];
        $queryParam = explode('&', $queryString);
        if (!empty($queryParam)) {
            foreach ($queryParam as $item) {
                $query               = explode('=', $item);
                $queryArr[$query[0]] = (int) $query[1];
            }
        }

        return $queryArr;
    }

    /**
     * @param string $dsn
     *
     * @return mixed[]
     */
    private static function regExWithUsersCredentials(string $dsn): array
    {
        preg_match(
            '/amqp:\/{2}([A-z, 0-9, .]+):(.*)@(?:([A-z, 0-9, .]+)|)(?:\/(?:[A-z, 0-9, .]+)|:((?:[0-9]+)|(?:env_.+))\/(?:([A-z, 0-9, .]+))|:(?:([0-9]+))|)(?:\?(.*)|)/',
            $dsn,
            $parsedUrl
        );

        return $parsedUrl;
    }

    /**
     * @param string $dsn
     *
     * @return mixed[]
     */
    private static function regExWithoutUsersCredentials(string $dsn): array
    {
        preg_match(
            '/amqp:\/{2}(?:([A-z, 0-9, .]+)|)(?:\/(?:[A-z, 0-9, .]+)|:((?:[0-9]+)|(?:env_.+))\/(?:([A-z, 0-9, .]+))|:(?:([0-9]+))|)(?:\?(.*)|)/',
            $dsn,
            $parsedUrl
        );

        return $parsedUrl;
    }

    /**
     * @param string[] $matches
     * @param string[] $result
     *
     * @return string
     */
    private static function parseRedisParameters(array $matches, array &$result): string
    {
        parse_str($matches[1], $params);

        foreach ($params as $key => $val) {
            if (!$val) {
                continue;
            }
            switch ($key) {
                case 'weight':
                    $result[self::WEIGHT] = (int) $val;

                    break;
                case 'alias':
                    $result[self::ALIAS] = $val;

                    break;
            }
        }

        return '';
    }

}
