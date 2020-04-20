<?php declare(strict_types=1);

namespace Hanaboso\Utils\String;

use JsonException;

/**
 * Class Json
 *
 * @package Hanaboso\Utils\String
 */
final class Json
{

    /**
     * @param mixed $data
     * @param int   $options
     *
     * @return string
     */
    public static function encode($data, int $options = JSON_THROW_ON_ERROR): string
    {
        return json_encode($data, $options) ?: '';
    }

    /**
     * @param string $data
     * @param int    $options
     *
     * @return mixed[]
     * @throws JsonException
     */
    public static function decode(string $data, int $options = JSON_THROW_ON_ERROR): array
    {
        return (array) json_decode($data, TRUE, 512, $options);
    }

}
