#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace NicoJust\AoC;

require __DIR__ . '../../vendor/autoload.php';

use NicoJust\AoC\Year2023\{
    Day01\AdventCommand as Day01Command,
    Day02\AdventCommand as Day02Command,
    Day03\AdventCommand as Day03Command,
    Day04\AdventCommand as Day04Command,
    Day05\AdventCommand as Day05Command,
    Day06\AdventCommand as Day06Command,
    Day07\AdventCommand as Day07Command,
    Day08\AdventCommand as Day08Command,
};
use Symfony\Component\Console\Application;

$application = new Application();

// Register Commands
$application->addCommands([
    new Day01Command(),
    new Day02Command(),
    new Day03Command(),
    new Day04Command(),
    new Day05Command(),
    new Day06Command(),
    new Day07Command(),
    new Day08Command(),
]);

$application->run();
