<?php declare(strict_types=1);

namespace Hanaboso\Utils\Utils;

use Exception;

/**
 * Class SafePathGenerator
 *
 * @package Hanaboso\Utils\Utils
 */
final class SafePathGenerator
{

    /**
     * @param int $levels
     * @param int $segment
     *
     * @return string
     * @throws Exception
     */
    public static function generate(int $levels, int $segment): string
    {
        $res      = '';
        $filename = base_convert(bin2hex(random_bytes(16)), 16, 36);

        $chunks = (array) str_split($filename, $segment);
        for ($i = 0; $i < $levels; $i++) {
            $res .= sprintf('%s%s', array_shift($chunks), DIRECTORY_SEPARATOR);
        }

        return $res;
    }

}