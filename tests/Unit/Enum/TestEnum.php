<?php declare(strict_types=1);

namespace UtilsTests\Unit\Enum;

use Hanaboso\Utils\Enum\EnumAbstract;

/**
 * Class TestEnum
 *
 * @package UtilsTests\Unit\Enum
 */
final class TestEnum extends EnumAbstract
{

    /**
     * @var string[]
     */
    public static array $choices = ['first' => '1st', 'second' => '2nd', 'third' => '3rd'];

}
