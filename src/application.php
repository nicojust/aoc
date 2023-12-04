#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Nicojust\Aoc;

require __DIR__ . '../../vendor/autoload.php';

use Nicojust\Aoc\Year2023\{
    Day01\AdventCommand as Day01Command,
    Day02\AdventCommand as Day02Command,
    Day03\AdventCommand as Day03Command,
    Day04\AdventCommand as Day04Command,
};
use Symfony\Component\Console\Application;

$application = new Application();

// Register Commands
$application->add(new Day01Command());
$application->add(new Day02Command());
$application->add(new Day03Command());
$application->add(new Day04Command());

$application->run();
