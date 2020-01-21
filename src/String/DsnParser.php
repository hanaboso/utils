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
