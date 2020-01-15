<?php declare(strict_types=1);

namespace UtilsTests\Unit\Validations;

use Hanaboso\Utils\Validations\Validations;
use LogicException;
use UtilsTests\KernelTestCaseAbstract;

/**
 * Class ValidationsTest
 *
 * @package UtilsTests\Unit\Validations
 *
 * @covers  \Hanaboso\Utils\Validations\Validations
 */
final class ValidationsTest extends KernelTestCaseAbstract
{

    /**
     * @covers       \Hanaboso\Utils\Validations\Validations::checkParams
     *
     * @dataProvider checkParamsProvider
     *
     * @param mixed[] $params
     * @param mixed[] $data
     * @param bool    $shouldBeOk
     */
    public function testCheckParams(array $params, array $data, bool $shouldBeOk): void
    {
        if (!$shouldBeOk) {
            self::expectException(LogicException::class);
        }

        Validations::checkParams($params, $data);
        self::assertTrue(TRUE);
    }

    /**
     * @return mixed[]
     */
    public function checkParamsProvider(): array
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
     * @covers       \Hanaboso\Utils\Validations\Validations::checkParamsAny
     *
     * @dataProvider checkParamsAnyProvider
     *
     * @param mixed[] $params
     * @param mixed[] $data
     * @param bool    $shouldBeOk
     */
    public function testCheckParamsAny(array $params, array $data, bool $shouldBeOk): void
    {
        if (!$shouldBeOk) {
            self::expectException(LogicException::class);
        }

        Validations::checkParamsAny($params, $data);
        self::assertTrue(TRUE);
    }

    /**
     * @return mixed[]
     */
    public function checkParamsAnyProvider(): array
    {
        return [
            [['param0', 'param1'], ['param1' => 1], TRUE],
            [['param0'], ['param0'], FALSE],
            [['param0' => ['param1', 'param2']], ['param0' => ['param2' => 1]], TRUE],
            [['param1', 'param0' => ['param1', 'param2']], ['param0' => ['param2' => 1]], TRUE],
            [['param0' => ['param1']], ['param1' => ['param1'], 'param0' => []], FALSE],
            [['param0' => ['param1']], [], FALSE],
            [['param0' => ['param1' => ['param2']]], ['param0' => ['param1' => ['param2' => 2]]], TRUE],
            [['param0' => 'param1'], ['param0' => ['param1' => ['param2' => 2]]], FALSE],
        ];
    }

    /**
     * @covers \Hanaboso\Utils\Validations\Validations::prepareTestParams
     */
    public function testPrepareTestParams(): void
    {
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
            $data
        );
    }

}
