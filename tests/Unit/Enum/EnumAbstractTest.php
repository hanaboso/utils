<?php declare(strict_types=1);

namespace UtilsTests\Unit\Enum;

use Hanaboso\Utils\Exception\EnumException;
use PHPUnit\Framework\TestCase;

/**
 * Class EnumAbstractTest
 *
 * @package UtilsTests\Unit\Enum
 */
final class EnumAbstractTest extends TestCase
{

    /**
     * @covers \Hanaboso\Utils\Enum\EnumAbstract::getChoices
     */
    public function testGetChoices(): void
    {
        self::assertEquals(['first' => '1st', 'second' => '2nd', 'third' => '3rd'], TestEnum::getChoices());
    }

    /**
     * @covers \Hanaboso\Utils\Enum\EnumAbstract::isValid
     *
     * @throws EnumException
     */
    public function testIsValid(): void
    {
        self::assertEquals('first', TestEnum::isValid('first'));
    }

    /**
     * @covers \Hanaboso\Utils\Enum\EnumAbstract::isValid
     *
     * @throws EnumException
     */
    public function testIsValidErr(): void
    {
        $this->expectException(EnumException::class);
        TestEnum::isValid('fourth');
    }

}
