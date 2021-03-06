<?php declare(strict_types=1);

namespace Hanaboso\Utils\System;

/**
 * Class PipesHeaders
 *
 * @package Hanaboso\Utils\System
 */
class PipesHeaders
{

    public const PF_PREFIX = 'pf-';

    // Framework headers
    public const CORRELATION_ID      = 'correlation-id';
    public const PROCESS_ID          = 'process-id';
    public const PARENT_ID           = 'parent-id';
    public const SEQUENCE_ID         = 'sequence-id';
    public const NODE_ID             = 'node-id';
    public const NODE_NAME           = 'node-name';
    public const TOPOLOGY_ID         = 'topology-id';
    public const TOPOLOGY_NAME       = 'topology-name';
    public const TOPOLOGY_DELETE_URL = 'topology-delete-url';
    public const RESULT_CODE         = 'result-code';
    public const RESULT_MESSAGE      = 'result-message';
    public const RESULT_DETAIL       = 'result-detail';
    public const REPEAT_QUEUE        = 'repeat-queue';
    public const REPEAT_INTERVAL     = 'repeat-interval';
    public const REPEAT_MAX_HOPS     = 'repeat-max-hops';
    public const REPEAT_HOPS         = 'repeat-hops';
    public const LIMIT_KEY           = 'limit-key';
    public const LIMIT_TIME          = 'limit-time';
    public const LIMIT_VALUE         = 'limit-value';
    public const LIMIT_LAST_UPDATE   = 'limit-last-update';
    public const CONTENT_TYPE        = 'content-type';
    public const PF_STOP             = 'stop';
    public const APPLICATION         = 'application';
    public const USER                = 'user';

    // --- MicroTimestamp because Bunny
    public const TIMESTAMP = 'published-timestamp';

    private const WHITE_LIST = ['content-type'];

    /**
     * @param string $key
     *
     * @return string
     */
    public static function createKey(string $key): string
    {
        return sprintf('%s%s', self::PF_PREFIX, $key);
    }

    /**
     * @param mixed[] $headers
     *
     * @return mixed[]
     */
    public static function clear(array $headers): array
    {
        return array_filter(
            $headers,
            static fn($key) => self::existPrefix($key) || in_array(strtolower($key), self::WHITE_LIST, TRUE),
            ARRAY_FILTER_USE_KEY,
        );
    }

    /**
     * @param string  $key
     * @param mixed[] $headers
     *
     * @return string|NULL
     */
    public static function get(string $key, array $headers): ?string
    {
        $header = $headers[sprintf('%s%s', self::PF_PREFIX, $key)] ?? NULL;

        if (is_array($header)) {
            $header = reset($header);
        }

        return !is_null($header) ? (string) $header : NULL;
    }

    /**
     * @param mixed[] $headers
     *
     * @return mixed[]
     */
    public static function debugInfo(array $headers): array
    {
        // Find debug header
        $debugInfo = array_filter(
            $headers,
            static fn($key) => self::existPrefix($key) &&
                in_array(
                    $key,
                    [
                        self::createKey(self::CORRELATION_ID),
                        self::createKey(self::NODE_ID),
                        self::createKey(self::TOPOLOGY_ID),
                        self::createKey(self::TOPOLOGY_NAME),
                        self::createKey(self::USER),
                        self::createKey(self::APPLICATION),
                    ],
                    TRUE,
                ),
            ARRAY_FILTER_USE_KEY,
        );

        // remove prefix from header
        foreach ($debugInfo as $key => $value) {
            $debugInfo[str_replace('-', '_', substr($key, strlen(self::PF_PREFIX)))] = $value;
            unset($debugInfo[$key]);
        }

        return $debugInfo;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    private static function existPrefix(string $key): bool
    {
        return str_starts_with($key, self::PF_PREFIX);
    }

}
