<?php declare(strict_types=1);

namespace UtilsTests\Unit\String;

use Exception;
use Hanaboso\Utils\String\SafePathGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Class SafePathGeneratorTest
 *
 * @package UtilsTests\Unit\String
 */
final class SafePathGeneratorTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testGenerate(): void
    {
        self::assertEquals(12, strlen(SafePathGenerator::generate(3, 3)));
        self::assertEquals(6, strlen(SafePathGenerator::generate(2, 2)));
    }

}
