<?php declare(strict_types=1);

namespace Hanaboso\Utils\Utils;

use Throwable;

/**
 * Class ExceptionContextLoader
 *
 * @package Hanaboso\Utils\Utils
 */
class ExceptionContextLoader
{

    /**
     * @param Throwable $e
     * @param mixed[]   $debugInfo
     *
     * @return mixed[]
     */
    public static function getContextForLogger(?Throwable $e = NULL, array $debugInfo = []): array
    {
        if ($e === NULL) {
            return [];
        }

        return [
            'exception' => $e,
            'message'   => $e->getMessage(),
            'trace'     => $e->getTraceAsString(),
            'code'      => $e->getCode(),
            'debugInfo' => $debugInfo,
        ];
    }

}
