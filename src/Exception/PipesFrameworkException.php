<?php declare(strict_types=1);

namespace Hanaboso\Utils\Exception;

/**
 * Class PipesFrameworkException
 *
 * @package Hanaboso\Utils\Exception
 */
class PipesFrameworkException extends PipesFrameworkExceptionAbstract
{

    public const UNKNOWN_ERROR                = 1;
    public const REQUIRED_PARAMETER_NOT_FOUND = 2;
    public const WRONG_VALUE                  = 3;

}
