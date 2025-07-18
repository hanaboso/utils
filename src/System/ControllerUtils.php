<?php declare(strict_types=1);

namespace Hanaboso\Utils\System;

use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\String\Json;
use Throwable;

/**
 * Class ControllerUtils
 *
 * @package Hanaboso\Utils\System
 */
class ControllerUtils
{

    public const string INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    public const string BAD_CREDENTIALS       = 'BAD_CREDENTIALS';
    public const string UNAUTHORIZED          = 'UNAUTHORIZED';
    public const string NOT_LOGGED            = 'NOT_LOGGED';
    public const string INVALID_REQUEST       = 'INVALID_REQUEST';
    public const string SERVICE_UNAVAILABLE   = 'SERVICE_UNAVAILABLE';
    public const string EMPTY                 = 'EMPTY';
    public const string NOT_FOUND             = 'NOT_FOUND';
    public const string INVALID_OPERATION     = 'INVALID_OPERATION';
    public const string ENTITY_ALREADY_EXISTS = 'ENTITY_ALREADY_EXISTS';
    public const string NOT_ALLOWED           = 'NOT_ALLOWED';

    /**
     * @param Throwable $e
     * @param string    $status
     *
     * @return string
     */
    public static function createExceptionData(Throwable $e, string $status = self::INTERNAL_SERVER_ERROR): string
    {
        $output = [
            'error_code' => $e->getCode(),
            'message'    => $e->getMessage(),
            'status'     => $status,
            'type'       => $e::class,
        ];

        return Json::encode($output);
    }

    /**
     * @param mixed[]        $headers
     * @param Throwable|NULL $e
     *
     * @return mixed[]
     */
    public static function createHeaders(array $headers = [], ?Throwable $e = NULL): array
    {
        $code    = 0;
        $message = '';
        $detail  = '';

        if ($e) {
            $code    = $e->getCode();
            $message = $e->getMessage();
            $detail  = Json::encode($e->getTraceAsString());
        }

        $array = [
            PipesHeaders::RESULT_CODE    => $code,
            PipesHeaders::RESULT_DETAIL  => $detail,
            PipesHeaders::RESULT_MESSAGE => $message,
        ];

        return array_merge($array, $headers);
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $data
     *
     * @throws PipesFrameworkException
     */
    public static function checkParameters(array $parameters, array $data): void
    {
        foreach ($parameters as $parameter) {
            if (!isset($data[$parameter])) {
                throw new PipesFrameworkException(
                    sprintf('Required parameter \'%s\' not found', $parameter),
                    PipesFrameworkException::REQUIRED_PARAMETER_NOT_FOUND,
                );
            }
        }
    }

}
