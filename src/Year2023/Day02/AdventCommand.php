<?php

declare(strict_types=1);

namespace NicoJust\AoC\Year2023\Day02;

use NicoJust\AoC\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:year:23:day:02',
    description: 'Day 2: Cube Conundrum (https://adventofcode.com/2023/day/2)',
    aliases: ['aoc:year:23:day:2', 'aoc:year:1:day:2'],
)]
class AdventCommand extends Command
{
    private const CUBES = [
        'red' => 12,
        'green' => 13,
        'blue' => 14,
    ];

    private array $games = [
        'bags' => [],
        'hits' => [],
        'misses' => [],
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

        $sumOfIds = 0;
        $sumOfPowerSets = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $line) {
            $output->write(sprintf('<comment>%s</comment>', $line));

            $this->processBag($line);
            $this->checkSets();
        }
        $output->writeln('');

        $sumOfIds = array_sum(array_keys($this->games['hits']));
        array_walk($this->games['bags'], function ($bag) use (&$sumOfPowerSets) {
            return $sumOfPowerSets += $bag['highest'][array_keys(self::CUBES)[0]] * $bag['highest'][array_keys(self::CUBES)[1]] * $bag['highest'][array_keys(self::CUBES)[2]];
        });

        $output->writeln(sprintf('<info>Solution 1: %d</info>', $sumOfIds));
        $output->writeln(sprintf('<info>Solution 2: %d</info>', $sumOfPowerSets));

        return Command::SUCCESS;
    }

    private function processBag(string $line): void
    {
        $part1 = explode(':', $line);
        $part2 = explode(';', str_replace($part1[0] . ':', '', $line));

        $this->games['bags'][$part1[0]]['id'] = (int)strstr($part1[0], ' ');
        $this->games['bags'][$part1[0]]['sets'] = array_map('trim', $part2);
    }

    private function checkSets(): void
    {
        foreach ($this->games['bags'] as &$bag) {
            $skip = false;

            foreach ($bag['sets'] as $set) {
                foreach (self::CUBES as $colorCube => $max) {
                    if (str_contains($set, $colorCube)) {
                        $position = strpos($set, $colorCube);
                        if ($position !== false) {
                            $atPos = ($position - 3 >= 0) ? $position - 3 : 0;
                            $amount = substr($set, $atPos, 2);

                            if ($amount > $max) {
                                $skip = true;
                            }

                            if (!isset($bag['highest'][$colorCube])) {
                                $bag['highest'][$colorCube] = 0;
                            }
                            if ($bag['highest'][$colorCube] < $amount) {
                                $bag['highest'][$colorCube] = $amount;
                            }
                        }
                    }
                }
            }

            if ($skip) {
                $this->games['misses'][$bag['id']] = $bag;
                continue;
            } else {
                $this->games['hits'][$bag['id']] = $bag;
            }
        }
    }
}
