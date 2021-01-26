<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity\Tests;

use PHPUnit\Framework\TestCase as PHPUnitFrameworkTestCase;

abstract class TestCase extends PHPUnitFrameworkTestCase
{
    public static function innerTrim($string): string
    {
        return trim(preg_replace('/>\s*</', '><', str_replace([
            "\r",
            "\n",
            "\t",
        ], '', $string)));
    }

    public static function assertMatches($pattern, $string)
    {
        parent::assertMatchesRegularExpression(sprintf('/%s/', str_replace('/', '\/', $pattern)), $string);
    }
}
