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
                'user'     => $parsedUrl[1],
                'password' => $parsedUrl[2],
                'host'     => $parsedUrl[3],
            ];

            if (!empty($queryArr)) {
                $result = array_merge($result, $queryArr);
            }

            if ((isset($parsedUrl[6]) && !empty($parsedUrl[6])) || (isset($parsedUrl[4]) && !empty($parsedUrl[4]))) {
                $result['port'] = isset($parsedUrl[6]) && !empty($parsedUrl[6]) ? $parsedUrl[6] : $parsedUrl[4];
            }

            if (isset($parsedUrl[5]) && !empty($parsedUrl[5])) {
                $result['vhost'] = $parsedUrl[5];
            }

            return $result;
        } else {
            $parsedUrl = self::regExWithoutUsersCredentials($dsn);

            if (isset($parsedUrl[5])) {
                $queryArr = self::getQueryParamsArr($parsedUrl[5]);
            }

            $result = [
                'host' => $parsedUrl[1],
            ];

            if (!empty($queryArr)) {
                $result = array_merge($result, $queryArr);
            }

            if ((isset($parsedUrl[2]) && !empty($parsedUrl[2])) || (isset($parsedUrl[4]) && !empty($parsedUrl[4]))) {
                $result['port'] = isset($parsedUrl[2]) && !empty($parsedUrl[2]) ? $parsedUrl[2] : $parsedUrl[4];
            }

            if ((isset($parsedUrl[3]) && !empty($parsedUrl[3]))) {
                $result['vhost'] = $parsedUrl[3];
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

}
