<?php declare(strict_types=1);

namespace UtilsTests\Unit\Traits;

use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class StaticClassTraitTest
 *
 * @package UtilsTests\Unit\Traits
 */
final class StaticClassTraitTest extends TestCase
{

    /**
     * @covers \UtilsTests\Unit\Traits\TestStaticTraits::__constructor
     */
    public function testStaticClass(): void
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage(sprintf('Class %s is static and cannot be instantiated.', TestStaticTraits::class));

        new TestStaticTraits();
    }

}
