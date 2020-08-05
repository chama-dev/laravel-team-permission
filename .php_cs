<?php

return (new MattAllan\LaravelCodeStyle\Config())->setFinder(
    PhpCsFixer\Finder::create()
        ->in([
            __DIR__.'/src',
            __DIR__.'/tests',
        ])
        ->name('*.php')
        ->notName('*.blade.php')
        ->ignoreDotFiles(true)
        ->ignoreVCS(true))->setRules(['@Laravel' => true]);
