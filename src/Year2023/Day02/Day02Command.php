<?php

namespace Nicojust\Aoc\Year2023\Day02;

use Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'aoc:day:02',
    description: 'Run code.',
    aliases: ['aoc:day2']
)]
class Day02Command extends Command
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
            ->addArgument('env', InputArgument::OPTIONAL, 'test or prod');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $env = $input->getArgument('env');

        $filepath = __DIR__ . '/input/prod.txt';
        if ($env) {
            $filepath = sprintf('%s/input/%s.txt', __DIR__, $env);
        }

        $filesystem = new Filesystem();
        if ($filesystem->exists($filepath)) {
            $sumOfIds = 0;
            $sumOfPowerSets = 0;

            foreach ($this->readLinesFromFile($filepath) as $line) {
                $output->writeln(sprintf('<comment>%s</comment>', $line));

                $this->processBag($line);
                $this->checkSets();
            }
            $output->writeln(sprintf('<info>%s</info>', print_r($this->games, true)));

            $sumOfIds = array_sum(array_keys($this->games['hits']));
            $output->writeln(sprintf('<info>Solution 1: %s</info>', $sumOfIds));

            array_walk($this->games['bags'], function ($bag) use (&$sumOfPowerSets) {
                return $sumOfPowerSets += $bag['highest'][array_keys(self::CUBES)[0]] * $bag['highest'][array_keys(self::CUBES)[1]] * $bag['highest'][array_keys(self::CUBES)[2]];
            });
            $output->writeln(sprintf('<info>Solution 2: %s</info>', $sumOfPowerSets));

            return Command::SUCCESS;
        }
        $output->writeln(sprintf('<error>File not found at "%s"</error>', $filepath));

        return Command::FAILURE;
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

    private function readLinesFromFile(string $filepath): Generator
    {
        $fileObject = new \SplFileObject($filepath, 'r');
        while (!$fileObject->eof()) {
            yield $fileObject->fgets();
        }

        $fileObject = null; // Release the file handle
    }
}
