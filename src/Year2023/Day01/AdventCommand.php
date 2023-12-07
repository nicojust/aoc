<?php

declare(strict_types=1);

namespace NicoJust\AoC\Year2023\Day01;

use NicoJust\AoC\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:year:23:day:01',
    description: 'Day 1: Trebuchet?! (https://adventofcode.com/2023/day/1)',
    aliases: ['aoc:year:23:day:1', 'aoc:year:1:day:1'],
)]
class AdventCommand extends Command
{
    private const CASES = [
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
    ];

    protected function configure(): void
    {
        $this
            ->addArgument(Util::ENV, InputArgument::OPTIONAL, 'test or prod');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Util::fileExists($input, $output, __DIR__)) {
            return Command::FAILURE;
        }

        $calibrationSum = 0;
        $calibrationSumWithWords = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $line) {
            $output->write(sprintf('<comment>%s</comment>', $line));

            $numbers = $this->getValueFromCase($line, true);
            reset($numbers);
            $number = current($numbers) . end($numbers);

            $numbersWithWords = $this->getValueFromCase($line, false);
            reset($numbersWithWords);
            $numberWithWord = current($numbersWithWords) . end($numbersWithWords);

            $calibrationSum += (int)$number;
            $calibrationSumWithWords += (int)$numberWithWord;
        }
        $output->writeln('');

        $output->writeln(sprintf('<info>Solution 1: %s</info>', $calibrationSum));
        $output->writeln(sprintf('<info>Solution 2: %s</info>', $calibrationSumWithWords));

        return Command::SUCCESS;
    }

    private function getValueFromCase(string $string, bool $onlyDigits = true): array
    {
        $positions = [];
        $cases = [];

        foreach (self::CASES as $key => $case) {
            if ($onlyDigits && !is_int($key)) {
                continue;
            }

            if (str_contains($string, (string)$key)) {
                $pos = strpos($string, (string)$key);
                while ($pos !== false) {
                    $positions[$key][] = $pos;
                    $pos = strpos($string, (string)$key, $pos + 1);
                }

                $cases[$key] = [
                    'count' => count($positions),
                    'positions' => $positions,
                    'key' => $key,
                    'case' => $case,
                ];

                // reset
                $positions = [];
            }
        }

        $combinedPositions = [];
        foreach ($cases as $case) {
            $combinedPositions = $combinedPositions + $case['positions'];
        }

        $numbers = [];
        foreach ($combinedPositions as $case => $positions) {
            $volatilePos = 0;
            foreach ($positions as $pos) {
                if ($volatilePos <= $pos) {
                    $numbers[$pos] = self::CASES[$case];
                    $volatilePos = $pos;
                }
            }
        }
        ksort($numbers);

        return $numbers;
    }
}
