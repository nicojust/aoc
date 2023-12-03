#!/usr/bin/env php
<?php

require __DIR__ . '../../vendor/autoload.php';

use Nicojust\Aoc\Year2023\Day01\AdventCommand as Day01Command;
use Nicojust\Aoc\Year2023\Day02\AdventCommand as Day02Command;
use Nicojust\Aoc\Year2023\Day03\AdventCommand as Day03Command;
use Symfony\Component\Console\Application;

$application = new Application();

// Register Commands
$application->add(new Day01Command());
$application->add(new Day02Command());
$application->add(new Day03Command());

$application->run();
