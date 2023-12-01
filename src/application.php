#!/usr/bin/env php
<?php

require __DIR__ . '../../vendor/autoload.php';

use Nicojust\Aoc\Year2023\Day01\Day01Command;
use Symfony\Component\Console\Application;

$application = new Application();

// Register Commands
$application->add(new Day01Command());

$application->run();
