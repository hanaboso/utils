<?php declare(strict_types=1);

namespace UtilsTests\Unit\Validations;

use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Hanaboso\Utils\Validations\Validations;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use UtilsTests\KernelTestCaseAbstract;

/**
 * Class ValidationsTest
 *
 * @package UtilsTests\Unit\Validations
 */
#[CoversClass(Validations::class)]
final class ValidationsTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    /**
     * @param mixed[] $params
     * @param mixed[] $data
     * @param bool    $shouldBeOk
     */
    #[DataProvider('checkParamsProvider')]
    public function testCheckParams(array $params, array $data, bool $shouldBeOk): void
    {
        if (!$shouldBeOk) {
            self::expectException(LogicException::class);
        }

        Validations::checkParams($params, $data);
        self::assertFake();
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $data
     * @param bool    $shouldBeOk
     */
    #[DataProvider('checkParamsAnyProvider')]
    public function testCheckParamsAny(array $params, array $data, bool $shouldBeOk): void
    {
        if (!$shouldBeOk) {
            self::expectException(LogicException::class);
        }

        Validations::checkParamsAny($params, $data);
        self::assertFake();
    }

    /**
     * @return void
     */
    public function testPrepareTestParams(): void
    {
        // @codingStandardsIgnoreLine
        $attrs = ['a', 'b' => ['c' => [['d', 'e']]], 'f'];

        $data = Validations::prepareTestParams($attrs);

        self::assertEquals(
            [
                'a' => 'a',
                'b' => [
                    'c' => [
                        [
                            'd' => 'd',
                            'e' => 'e',
                        ],

                    ],
                ],
                'f' => 'f',
            ],
            $data,
        );
    }

    /**
     * @return mixed[]
     */
    public static function checkParamsProvider(): array
    {
        return [
            [['param0', 'param1'], ['param0' => 0, 'param1' => 1], TRUE],
            [['param0'], ['param0'], FALSE],
            [['param0' => ['param1']], ['param0' => ['param1' => 1]], TRUE],
            [['param0' => ['param1']], ['param0' => []], FALSE],
            [['param0' => ['param1']], [], FALSE],
            [['param0' => ['param1' => ['param2']]], ['param0' => ['param1' => ['param2' => 2]]], TRUE],
            [['param0' => 'param1'], ['param0' => ['param1' => ['param2' => 2]]], FALSE],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function checkParamsAnyProvider(): array
    {
        return [
            [['param0', 'param1'], ['param1' => 1], TRUE],
            [['param0'], ['param0'], FALSE],
            [['param0' => ['param1', 'param2']], ['param0' => ['param2' => 1]], TRUE],
            // @codingStandardsIgnoreLine
            [['param1', 'param0' => ['param1', 'param2']], ['param0' => ['param2' => 1]], TRUE],
            [['param0' => ['param1']], ['param1' => ['param1'], 'param0' => []], FALSE],
            [['param0' => ['param1']], [], FALSE],
            [['param0' => ['param1' => ['param2']]], ['param0' => ['param1' => ['param2' => 2]]], TRUE],
            [['param0' => 'param1'], ['param0' => ['param1' => ['param2' => 2]]], FALSE],
        ];
    }

}
