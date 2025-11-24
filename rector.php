<?php

declare(strict_types=1);

use Rector\ValueObject\PhpVersion;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/src',
		__DIR__ . '/tests',
		__DIR__ . '/db',
	])
    ->withPhpVersion(PhpVersion::PHP_74)
	->withPhp74Sets()
    ->withImportNames()
	->withSets([
		SetList::TYPE_DECLARATION,
		SetList::DEAD_CODE,
		SetList::CODE_QUALITY,
        SetList::EARLY_RETURN,
	]);
