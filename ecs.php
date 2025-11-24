<?php

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Basic\CurlyBracesPositionFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use Symplify\CodingStandard\Fixer\Annotation\RemovePHPStormAnnotationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
	->withPaths([
		__DIR__ . '/src/',
		__DIR__ . '/tests/',
	])
	->withSkip([
		__DIR__ . '/tests/Support/_generated',
	])
	->withSets([SetList::PSR_12])
	->withSpacing(Option::INDENTATION_TAB, PHP_EOL)
	->withRules([RemovePHPStormAnnotationFixer::class])
	->withConfiguredRule(
		ArraySyntaxFixer::class,
		['syntax' => 'short']
	)
	->withConfiguredRule(
		CurlyBracesPositionFixer::class,
		[
			'classes_opening_brace' => 'same_line',
			'functions_opening_brace' => 'same_line',
		]
	)
	->withConfiguredRule(
		VisibilityRequiredFixer::class,
		['elements' => ['property', 'method']]
	);
