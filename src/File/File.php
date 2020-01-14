<?php declare(strict_types=1);

namespace Hanaboso\Utils\File;

use LogicException;

/**
 * Class File
 *
 * @package Hanaboso\Utils\File
 */
final class File
{

    /**
     * @param string $filename
     * @param mixed  $data
     *
     * @return int
     */
    public static function putContent(string $filename, $data): int
    {
        $res = @file_put_contents($filename, $data);
        if ($res === FALSE) {
            throw new LogicException('Put content returned FALSE');
        }

        return $res;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function getContent(string $filename): string
    {
        $res = @file_get_contents($filename);
        if ($res === FALSE) {
            throw new LogicException('File not found');
        }

        return $res;
    }

}
