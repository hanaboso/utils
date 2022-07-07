<?php declare(strict_types=1);

namespace Hanaboso\Utils\System;

/**
 * Class PipesHeaders
 *
 * @package Hanaboso\Utils\System
 */
class PipesHeaders
{

    // Framework headers
    public const CORRELATION_ID     = 'correlation-id';
    public const PROCESS_ID         = 'process-id';
    public const PARENT_ID          = 'parent-id';
    public const SEQUENCE_ID        = 'sequence-id';
    public const NODE_ID            = 'node-id';
    public const NODE_NAME          = 'node-name';
    public const TOPOLOGY_ID        = 'topology-id';
    public const TOPOLOGY_NAME      = 'topology-name';
    public const APPLICATION        = 'application';
    public const USER               = 'user';
    public const WORKER_FOLLOWERS   = 'worker-followers';
    public const FORCE_TARGET_QUEUE = 'force-target-queue';

    // Result headers
    public const RESULT_CODE    = 'result-code';
    public const RESULT_MESSAGE = 'result-message';
    public const RESULT_DETAIL  = 'result-detail';

    // Repeater headers
    public const REPEAT_QUEUE    = 'repeat-queue';
    public const REPEAT_INTERVAL = 'repeat-interval';
    public const REPEAT_MAX_HOPS = 'repeat-max-hops';
    public const REPEAT_HOPS     = 'repeat-hops';

    // Limiter headers
    public const LIMITER_KEY = 'limiter-key';

    // Batch headers
    public const BATCH_CURSOR = 'cursor';

    // --- MicroTimestamp because Bunny
    public const TIMESTAMP = 'published-timestamp';

    private const WHITE_LIST = ['content-type'];

    /**
     * @param mixed[] $headers
     *
     * @return mixed[]
     */
    public static function clear(array $headers): array
    {
        return array_filter(
            $headers,
            static fn($key) => in_array(strtolower($key), self::WHITE_LIST, TRUE),
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
        $header = $headers[$key] ?? NULL;

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
            static fn($key) => in_array(
                $key,
                [
                    self::CORRELATION_ID,
                    self::NODE_ID,
                    self::TOPOLOGY_ID,
                    self::TOPOLOGY_NAME,
                    self::USER,
                    self::APPLICATION,
                ],
                TRUE,
            ),
            ARRAY_FILTER_USE_KEY,
        );

        return $debugInfo;
    }

}
