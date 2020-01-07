<?php declare(strict_types=1);

namespace Hanaboso\Utils\String;

/**
 * Class Base64
 *
 * @package Hanaboso\Utils\String
 */
final class Base64
{

    /**
     * @param string $inputStr
     *
     * @return string
     */
    public static function base64UrlEncode(string $inputStr): string
    {
        return strtr(base64_encode($inputStr), '+/=', '-_,');
    }

    /**
     * @param string $inputStr
     *
     * @return string
     */
    public static function base64UrlDecode(string $inputStr): string
    {
        return (string) base64_decode(strtr($inputStr, '-_,', '+/='));
    }

}