<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
    ])

    ->withRules([
        NoUnusedImportsFixer::class
    ])

    ->withSets([
        SetList::PSR_12,
//        SetList::ARRAY,
//        SetList::CLEAN_CODE,
//        SetList::SYMPLIFY,
//        SetList::COMMENTS,
//        SetList::DOCTRINE_ANNOTATIONS,
//        SetList::NAMESPACES
    ])

     ;
