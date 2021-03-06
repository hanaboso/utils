<?php declare(strict_types=1);

namespace Hanaboso\Utils\System;

use Hanaboso\Utils\String\Strings;

/**
 * Class NodeGeneratorUtils
 *
 * @package Hanaboso\Utils\System
 */
final class NodeGeneratorUtils
{

    /**
     * @param string $id
     * @param string $name
     *
     * @return string
     */
    public static function normalizeName(string $id, string $name): string
    {
        return sprintf('%s-%s', $id, Strings::webalize($name));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function createServiceName(string $name): string
    {
        $pieces = [];
        $i      = 0;
        foreach (explode('-', $name) as $item) {
            if ($i === 0) {
                $pieces[] = $item;
            } else {
                $pieces[] = substr($item, 0, 3);
            }
            $i++;
        }

        return substr(implode('-', $pieces), 0, 63);
    }

    /**
     * @param string $id
     * @param string $name
     *
     * @return string
     */
    public static function createNormalizedServiceName(string $id, string $name): string
    {
        return self::createServiceName(self::normalizeName($id, $name));
    }

    /**
     * @param string $topologyId
     * @param string $nodeId
     * @param string $nodeName
     *
     * @return string
     */
    public static function generateQueueName(string $topologyId, string $nodeId, string $nodeName): string
    {
        return sprintf(
            'pipes.%s.%s',
            $topologyId,
            self::createNormalizedServiceName($nodeId, $nodeName),
        );
    }

    /**
     * @param string $topology
     * @param string $nodeId
     * @param string $nodeName
     *
     * @return string
     */
    public static function generateQueueNameFromStrings(string $topology, string $nodeId, string $nodeName): string
    {
        return sprintf(
            'pipes.%s.%s',
            $topology,
            self::createNormalizedServiceName($nodeId, $nodeName),
        );
    }

}
