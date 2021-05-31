<?php declare(strict_types=1);

namespace UtilsTests\Unit\Traits;

use PHPUnit\Framework\TestCase;

/**
 * Class UrlBuilderTraitTest
 *
 * @package UtilsTests\Unit\Traits
 */
final class UrlBuilderTraitTest extends TestCase
{

    /**
     * @covers \UtilsTests\Unit\Traits\TestTraits::getUrl
     */
    public function testGetUrl(): void
    {
        $test = new TestTraits();
        $test->setHost('http://localhost');
        self::assertEquals(
            'http://localhost/first/second/third',
            $test->getUrlForTest('first/%s/%s', 'second', 'third'),
        );
    }

}
