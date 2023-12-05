<?php

declare(strict_types=1);

namespace Nicojust\Aoc\Year2023\Day05;

use Nicojust\Aoc\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:day:05',
    description: 'Day 5: If You Give A Seed A Fertilizer',
    aliases: ['aoc:day5']
)]
class AdventCommand extends Command
{
    private array $seeds = [];
    private array $maps = [];

    private int $parseKey = 0;
    private int $parseKeyMap = 0;
    private bool $shouldParseData = false;
    private array $parseValues = [
        'from' => '',
        'to' => '',
        'map' => [],
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

        $lowestLocation = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $line) {
            $output->write(sprintf('<comment>%s</comment>', $line));

            if ($this->parseAlmanac($line)) {
                $this->maps[$this->parseKey++] = $this->parseValues;

                // reset
                $this->parseValues = [
                    'from' => '',
                    'to' => '',
                    'map' => [],
                ];
            }
        }
        $this->maps[$this->parseKey++] = $this->parseValues;
        $output->writeln('');

        $lowestLocation = min($this->findSeedLocations($this->seeds));
        $output->writeln(sprintf('<info>Solution 1: %d</info>', $lowestLocation));

        return Command::SUCCESS;
    }

    private function parseAlmanac(string $line): bool
    {
        $saveToMap = false;
        if (str_contains($line, 'seeds: ')) {
            $this->seeds = array_map('intval', explode(' ', str_replace('seeds: ', '', $line)));
        }

        if (!$this->shouldParseData && str_contains($line, '-')) {
            $this->shouldParseData = true;
            $locations = explode('-', $line);

            [
                $this->parseValues['from'],
                $this->parseValues['to']
            ] = [
                $locations[0],
                trim(str_replace(' map:', '', $locations[2]))
            ];
        } elseif ($this->shouldParseData && $line === PHP_EOL) {
            $this->shouldParseData = false;
            $saveToMap = true;
        } elseif ($this->shouldParseData) {
            $ranges = array_map('intval', explode(' ', $line));

            [
                $this->parseValues['map'][$this->parseKeyMap]['dest'],
                $this->parseValues['map'][$this->parseKeyMap]['src'],
                $this->parseValues['map'][$this->parseKeyMap]['range']
            ] = [
                $ranges[0],
                $ranges[1],
                $ranges[2]
            ];

            $this->parseKeyMap++;
        }

        return $saveToMap;
    }

    private function findSeedLocations($seeds = []): array
    {
        $locations = [];

        foreach ($seeds as $origSeed) {
            $seed = $origSeed;

            foreach ($this->maps as $maps) {
                foreach ($maps['map'] as $map) {
                    if ($seed >= $map['src'] && $seed < ($map['src'] + $map['range'])) {
                        $diff = $map['dest'] - $map['src'];
                        $seed += $diff;
                        break;
                    }
                }
            }

            $locations[] = $seed;
        }

        return $locations;
    }
}
