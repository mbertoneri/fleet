<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        '@PHP82Migration' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('src')
            ->in('tests')
    );
