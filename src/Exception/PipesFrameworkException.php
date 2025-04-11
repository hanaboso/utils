<?php declare(strict_types=1);

namespace Hanaboso\Utils\Exception;

/**
 * Class PipesFrameworkException
 *
 * @package Hanaboso\Utils\Exception
 */
class PipesFrameworkException extends PipesFrameworkExceptionAbstract
{

    public const int UNKNOWN_ERROR                = 1;
    public const int REQUIRED_PARAMETER_NOT_FOUND = 2;
    public const int WRONG_VALUE                  = 3;

}
