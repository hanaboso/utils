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
    public const string CORRELATION_ID     = 'correlation-id';
    public const string PROCESS_ID         = 'process-id';
    public const string PARENT_ID          = 'parent-id';
    public const string SEQUENCE_ID        = 'sequence-id';
    public const string NODE_ID            = 'node-id';
    public const string NODE_NAME          = 'node-name';
    public const string TOPOLOGY_ID        = 'topology-id';
    public const string TOPOLOGY_NAME      = 'topology-name';
    public const string APPLICATION        = 'application';
    public const string USER               = 'user';
    public const string WORKER_FOLLOWERS   = 'worker-followers';
    public const string FORCE_TARGET_QUEUE = 'force-target-queue';

    // Result headers
    public const string RESULT_CODE    = 'result-code';
    public const string RESULT_MESSAGE = 'result-message';
    public const string RESULT_DETAIL  = 'result-detail';

    // Repeater headers
    public const string REPEAT_QUEUE    = 'repeat-queue';
    public const string REPEAT_INTERVAL = 'repeat-interval';
    public const string REPEAT_MAX_HOPS = 'repeat-max-hops';
    public const string REPEAT_HOPS     = 'repeat-hops';

    // Limiter headers
    public const string LIMITER_KEY = 'limiter-key';

    // Batch headers
    public const string BATCH_CURSOR = 'cursor';

    // --- MicroTimestamp because Bunny
    public const string TIMESTAMP = 'published-timestamp';

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
        return array_filter(
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
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function decorateLimitKey(string $key): string
    {
        if (!str_contains($key, '|')) {
            return sprintf('%s|', $key);
        }

        return $key;
    }

    /**
     * @param string|null $limitKey
     *
     * @return mixed[]
     */
    public static function parseLimitKey(?string $limitKey): array
    {
        if (!$limitKey) {
            return [];
        }

        $split = explode(';', $limitKey);

        $parsedLimits = [];
        for ($i = 0; $i < count($split); $i += 3) {
            $parsedLimits[$split[$i]] = sprintf('%s;%s;%s', $split[$i], $split[$i + 1], $split[$i + 2]);
        }

        return $parsedLimits;
    }

    /**
     * @param string $key
     * @param int    $time
     * @param int    $value
     *
     * @return string
     */
    public static function getLimiterKey(string $key, int $time, int $value): string
    {
        return sprintf('%s;%s;%s', self::decorateLimitKey($key), $time, $value);
    }

    /**
     * @param string $key
     * @param int    $time
     * @param int    $amount
     * @param string $groupKey
     * @param int    $groupTime
     * @param int    $groupAmount
     *
     * @return string
     */
    public static function getLimiterKeyWithGroup(
        string $key,
        int $time,
        int $amount,
        string $groupKey,
        int $groupTime,
        int $groupAmount,
    ): string
    {
        return sprintf(
            '%s;%s;%s;%s;%s;%s',
            self::decorateLimitKey($key),
            $time,
            $amount,
            self::decorateLimitKey($groupKey),
            $groupTime,
            $groupAmount,
        );
    }

}
