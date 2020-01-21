<?php declare(strict_types=1);

namespace Hanaboso\Utils\Exception;

/**
 * Class EnumException
 *
 * @package Hanaboso\Utils\Exception
 */
final class EnumException extends PipesFrameworkExceptionAbstract
{

    public const INVALID_CHOICE = self::OFFSET + 1;

    protected const OFFSET = 2_000;

}
