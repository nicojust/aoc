<?php

namespace Nicojust\Aoc\Year2023\Day03;

use Nicojust\Aoc\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:day:03',
    description: 'Day 3: Gear Ratios',
    aliases: ['aoc:day3']
)]
class AdventCommand extends Command
{
    private array $schematics = [];

    private array $directions = [
        [-1, -1],
        [-1, 0],
        [-1, +1],
        [0, -1],
        [0, +1],
        [+1, -1],
        [+1, 0],
        [+1, +1],
    ];

    private int $id = 0;

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

        $sumOfNumbersWithAdjacentSymbols = 0;
        $sumOfRatiosWithAdjacentStars = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $line) {
            $output->write(sprintf('<comment>%s</comment>', $line));

            $this->addSchematic($line);
        }
        $output->writeln('');

        [$hits, $ratios] = $this->checkSchematics();
        $sumOfNumbersWithAdjacentSymbols = array_sum($hits);
        $sumOfRatiosWithAdjacentStars = array_sum($ratios);

        $output->writeln(sprintf('<comment>%s</comment>', print_r($hits, true)));
        $output->writeln(sprintf('<comment>%s</comment>', print_r($ratios, true)));

        $output->writeln(sprintf('<info>Solution 1: %d</info>', $sumOfNumbersWithAdjacentSymbols));
        $output->writeln(sprintf('<info>Solution 2: %d</info>', $sumOfRatiosWithAdjacentStars));

        return Command::SUCCESS;
    }

    private function addSchematic(string $line): void
    {
        preg_match_all('/\d+/', $line, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches as &$match) {
            foreach ($match as &$value) {
                $addPos = strlen($value[0]) - 1;
                $pos = [];
                foreach (range(0, $addPos) as $i) {
                    $pos[] = $value[1] + $i;
                }
                $value[1] = $pos;
                $value[2] = $this->id++;
            }
        }

        $this->schematics[] = [
            'schema' => str_split(trim($line)),
            'values' => $matches[0],
        ];
    }

    private function checkSchematics(): array
    {
        $hits = [];
        $ratioHits = [];
        $ratios = [];

        foreach ($this->schematics as $key => $schema) {
            foreach ($schema['schema'] as $pos => $char) {
                $volatileHits = [];

                if (!ctype_digit($char) && $char !== '.') {
                    foreach ($this->directions as $directions) {
                        if (isset($this->schematics[$key + ($directions[0])]['schema'][$pos + ($directions[1])]) && ctype_digit($this->schematics[$key + ($directions[0])]['schema'][$pos + ($directions[1])])) {
                            foreach ($this->schematics[$key + ($directions[0])]['values'] as $value) {
                                if (in_array($pos + ($directions[1]), $value[1])) {
                                    $hits[$value[2]] = $value[0];
                                    if ($char === '*') {
                                        $volatileHits[$value[2]] = $value[0];
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }

                if (count($volatileHits) === 2) {
                    $ratioHits[] = $volatileHits;
                }
            }
        }

        foreach ($ratioHits as $rHit) {
            $normalizedHit = array_values($rHit);

            $ratios[] = $normalizedHit[0] * $normalizedHit[1];
        }

        return [$hits, $ratios];
    }
}
