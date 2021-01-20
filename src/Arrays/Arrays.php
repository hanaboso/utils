<?php declare(strict_types=1);

namespace Hanaboso\Utils\Arrays;

/**
 * Class Arrays
 *
 * @package Hanaboso\Utils\Arrays
 */
final class Arrays
{

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function isList($value): bool
    {
        return is_array($value) && (!$value || array_keys($value) === range(0, count($value) - 1));
    }

    /**
     * @see https://github.com/charliekassel/array-diff/blob/master/src/ArrayDiff.php
     *
     * @param mixed[] $old
     * @param mixed[] $new
     *
     * @return mixed[]
     */
    public static function diff(array $old, array $new): array
    {
        $res     = [];
        $created = self::getCreatedKeys($old, $new);
        $updated = self::getUpdatedKeys($old, $new);
        $deleted = self::getRemovedKeys($old, $new);

        if (!empty($created)) {
            $res['created'] = $created;
        }

        if (!empty($updated)) {
            $res['updated'] = $updated;
        }

        if (!empty($deleted)) {
            $res['deleted'] = $deleted;
        }

        return $res;
    }

    /**
     * @param mixed[] $old
     * @param mixed[] $new
     *
     * @return mixed[]
     */
    private static function getCreatedKeys(array $old, array $new): array
    {
        return array_filter($new, static fn($key): bool => !array_key_exists($key, $old), ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param mixed[] $old
     * @param mixed[] $new
     *
     * @return mixed[]
     */
    private static function getRemovedKeys(array $old, array $new): array
    {
        return array_filter($old, static fn($key): bool => !array_key_exists($key, $new), ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param mixed[] $old
     * @param mixed[] $new
     *
     * @return mixed[]
     */
    private static function getUpdatedKeys(array $old, array $new): array
    {
        $changed = array_filter(
            $new,
            static fn($newItem, $key): bool => array_key_exists($key, $old) && $old[$key] !== $newItem,
            ARRAY_FILTER_USE_BOTH
        );

        array_walk(
            $changed,
            static function (&$changedItem, $key) use ($old): void {
                if (is_array($changedItem) && !is_null($old[$key])) {
                    $changedItem = self::diff($old[$key], $changedItem);
                } else {
                    $changedItem = [
                        'old' => $old[$key],
                        'new' => $changedItem,
                    ];
                }
            }
        );

        return $changed;
    }

}
