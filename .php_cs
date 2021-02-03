<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules(array(
        '@PhpCsFixer' => true,
    ))
    ->setFinder($finder);
