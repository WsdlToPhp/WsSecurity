<?php
namespace WsdlToPhp\WsSecurity\Tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public static function innerTrim($string)
    {
        return trim(preg_replace('/>\s*</', '><', str_replace(array(
            "\r",
            "\n",
            "\t"
        ), '', $string)));
    }
    public static function assertMatches($pattern, $string)
    {
        return parent::assertTrue(preg_match('/' . str_replace('/', '\/', $pattern) . '/', $string) > 0, $string);
    }
}
