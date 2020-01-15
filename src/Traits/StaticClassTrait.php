<?php declare(strict_types=1);

namespace Hanaboso\Utils\Traits;

use LogicException;

/**
 * Trait StaticClassTrait
 *
 * @package Hanaboso\Utils\Traits
 */
trait StaticClassTrait
{

    /**
     * StaticClass constructor.
     *
     * @throws LogicException
     */
    public function __construct()
    {
        throw new LogicException(sprintf('Class %s is static and cannot be instantiated.', static::class));
    }

}
