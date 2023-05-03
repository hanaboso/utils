<?php declare(strict_types=1);

namespace Hanaboso\Utils\String;

/**
 * Class UriParams
 *
 * @package Hanaboso\Utils\String
 */
final class UriParams
{

    /**
     * @param string|NULL $orderBy
     *
     * @return mixed[]
     */
    public static function parseOrderBy(?string $orderBy = NULL): array
    {
        $convertTable = [
            '+' => 'ASC',
            '-' => 'DESC',
        ];

        $sort = [];

        if (!empty($orderBy)) {
            foreach (explode(',', $orderBy) as $item) {
                $name        = substr($item, 0, -1);
                $direction   = substr($item, -1);
                $sort[$name] = $convertTable[$direction];
            }
        }

        return $sort;
    }

}
