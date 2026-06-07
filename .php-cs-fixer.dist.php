<?php

    declare(strict_types=1);

    $finder = PhpCsFixer\Finder::create()
        ->in(__DIR__)
        ->exclude([
            '.git',
            '.github',
            '.idea',
            '.vscode',
            'vendor',
            'uploads',
            'cache',
        ])
        ->notPath([
            'assets/widgets/payment',
            'assets/widgets/payment_old',
            'assets/widgets/cart/PagSeguro',
            'assets/widgets/cart/Moip',
        ])
        ->name('*.php');

    return (new PhpCsFixer\Config())
        ->setRiskyAllowed(false)
        ->setUsingCache(true)
        ->setRules([
            '@PSR12' => true,
            'no_alternative_syntax' => ['fix_non_monolithic_code' => true],
            'ordered_imports' => ['sort_algorithm' => 'alpha'],
            'single_import_per_statement' => true,
            'no_unused_imports' => true,
        ])
        ->setFinder($finder);
