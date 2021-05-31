<?php declare(strict_types=1);

namespace Hanaboso\Utils\Validations;

use Hanaboso\Utils\String\Json;
use LogicException;

/**
 * Class Validations
 *
 * @package Hanaboso\Utils\Validations
 */
final class Validations
{

    /**
     * @param mixed[] $params
     * @param mixed[] $data
     * @param bool    $logBody
     */
    public static function checkParams(array $params, array $data, bool $logBody = FALSE): void
    {
        foreach ($params as $key => $param) {
            if (is_array($param)) {
                Validations::checkParams($param, $data[$key] ?? []);

                continue;
            }
            if (!array_key_exists($param, $data)) {
                throw new LogicException(
                    sprintf(
                        'Missing required parameter [%s]%s',
                        $param,
                        $logBody ? sprintf(', received: [%s]', Json::encode($data)) : '',
                    ),
                );
            }
        }
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $data
     * @param bool    $logBody
     */
    public static function checkParamsAny(array $params, array $data, bool $logBody = FALSE): void
    {
        $found    = FALSE;
        $searched = [];

        foreach ($params as $key => $param) {
            $searched[] = $param;
            if (is_array($param)) {
                Validations::checkParamsAny($param, $data[$key] ?? []);
                $found = TRUE;

                continue;
            }

            if (array_key_exists($param, $data)) {
                $found = TRUE;
            }
        }

        if (!$found) {
            throw new LogicException(
                sprintf(
                    'Missing at least one of required parameters [%s]%s',
                    implode(', ', $searched),
                    $logBody ? sprintf(', received: [%s]', Json::encode($data)) : '',
                ),
            );
        }
    }

    /**
     * @param mixed[] $atts
     *
     * @return mixed[]
     */
    public static function prepareTestParams(array $atts): array
    {
        $res = [];
        foreach ($atts as $key => $att) {
            if (is_array($att)) {
                $res[$key] = self::prepareTestParams($att);
            } else {
                $res[$att] = $att;
            }
        }

        return $res;
    }

}
