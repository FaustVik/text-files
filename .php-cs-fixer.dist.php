<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@PSR12' => true,
        '@PHP84Migration' => true,

        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,

        'binary_operator_spaces' => ['default' => 'single_space'],
        'concat_space' => ['spacing' => 'one'],

        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'whitespace_after_comma_in_array' => true,

        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_trim' => true,

        'no_unused_imports' => true,
        'no_empty_statement' => true,
        'no_useless_return' => true,

        'nullable_type_declaration_for_default_null_value' => true,
        'return_type_declaration' => true,

        'no_extra_blank_lines' => true,
        'single_blank_line_at_eof' => true,
        'no_trailing_whitespace' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache');
