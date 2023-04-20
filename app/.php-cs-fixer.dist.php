<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'var',
        'public'
    ])
    ->notPath([
        'src/Kernel.php',
        'tests/bootstrap.php',
        'tests/object-manager.php'
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PHP81Migration' => true,
        '@DoctrineAnnotation' => true,
    ])
    ->setFinder($finder);
