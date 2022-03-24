<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setUsingCache(false)
    ->setRules(array(
        '@PhpCsFixer' => true,
    ))
    ->setFinder($finder);
