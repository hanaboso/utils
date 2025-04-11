<?php declare(strict_types=1);

namespace UtilsTests\Unit\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class UrlBuilderTraitTest
 *
 * @package UtilsTests\Unit\Traits
 */
#[CoversClass(TestTraits::class)]
final class UrlBuilderTraitTest extends TestCase
{

    /**
     * @return void
     */
    public function testGetUrl(): void
    {
        $test = new TestTraits();
        $test->setHost('http://localhost');
        self::assertSame(
            'http://localhost/first/second/third',
            $test->getUrlForTest('first/%s/%s', 'second', 'third'),
        );
    }

}
