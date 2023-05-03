<?php declare(strict_types=1);

namespace UtilsTests\Unit\File;

use Hanaboso\Utils\File\File;
use LogicException;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

/**
 * Class FilesTest
 *
 * @package UtilsTests\Unit\File
 */
final class FilesTest extends TestCase
{

    use PHPMock;

    public const PUT_NAME = 'w_name.txt';
    public const GET_NAME = 'r_name.txt';

    /**
     * @covers \Hanaboso\Utils\File\File::putContent
     */
    public function testPutContentFailed(): void
    {
        $this
            ->getFunctionMock('Hanaboso\Utils\File', 'file_put_contents')
            ->expects(self::any())
            ->willReturnCallback(static fn() => FALSE);

        self::expectException(LogicException::class);
        File::putContent(self::PUT_NAME, 'awdad');

        unlink(self::PUT_NAME);
    }

    /**
     * @covers \Hanaboso\Utils\File\File::putContent
     */
    public function testPutContent(): void
    {
        $res = File::putContent(self::PUT_NAME, 'awdad');
        self::assertEquals(5, $res);

        unlink(self::PUT_NAME);
    }

    /**
     * @covers \Hanaboso\Utils\File\File::putContent
     * @covers \Hanaboso\Utils\File\File::getContent
     */
    public function testGetContent(): void
    {
        $content = 'awdad';
        File::putContent(self::GET_NAME, $content);
        $res = File::getContent(self::GET_NAME);
        self::assertEquals($content, $res);

        unlink(self::GET_NAME);
    }

    /**
     * @covers \Hanaboso\Utils\File\File::getContent
     */
    public function testGetContentNotFound(): void
    {
        self::expectException(LogicException::class);
        File::getContent(self::GET_NAME);

        unlink(self::GET_NAME);
    }

}
