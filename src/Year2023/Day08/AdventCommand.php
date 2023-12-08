<?php

declare(strict_types=1);

namespace NicoJust\AoC\Year2023\Day08;

use NicoJust\AoC\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:year:23:day:08',
    description: 'Day 8: Haunted Wasteland (https://adventofcode.com/2023/day/8)',
    aliases: ['aoc:year:23:day:8', 'aoc:year:1:day:8'],
)]
class AdventCommand extends Command
{
    private const START_NODE = 'AAA';
    private const END_NODE = 'ZZZ';

    private array $steps = [];
    private array $network = [];

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

        $stepsTaken = 0;
        $stepsTakenSimultaneously = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $key => $line) {
            $output->write(sprintf('<comment>%s</comment>', $line));

            $this->parseNetwork($line, $key);
        }
        $output->writeln('');

        $stepsTaken = $this->walkNodes($this->network, $this->steps);
        $stepsTakenSimultaneously = $this->findLCM($this->walkNodesSimultaneously($this->network, $this->steps));

        $output->writeln(sprintf('<info>Solution 1: %d</info>', $stepsTaken));
        $output->writeln(sprintf('<info>Solution 2: %d</info>', $stepsTakenSimultaneously));

        return Command::SUCCESS;
    }

    private function parseNetwork(string $line, int $key): void
    {
        if ($key === 0) {
            $directions = [];
            foreach (str_split($line) as $dir) {
                switch (true) {
                    case $dir === 'L':
                        $directions[] = 0;
                        break;
                    case $dir === 'R':
                        $directions[] = 1;
                        break;
                }
            }

            $this->steps = $directions;
        }

        if (str_contains($line, '=')) {
            [$node, $destinations] = explode(' = ', $line);
            $paths = explode(',', trim(preg_replace("/\s+/", '', $destinations), '()'));

            $this->network[$node] = $paths;
        }
    }

    private function walkNodes(array $network = [], array $steps): int
    {
        $stepsTaken = 0;
        $currentNode = self::START_NODE;

        // safe guard
        if (!in_array($currentNode, array_keys($network), true)) {
            return $stepsTaken;
        }

        while ($currentNode !== self::END_NODE) {
            foreach ($steps as $dir) {
                $stepsTaken += 1;
                $currentNode = $network[$currentNode][$dir];

                if ($currentNode === self::END_NODE) {
                    break 2;
                }
            }
        }

        return $stepsTaken;
    }

    private function walkNodesSimultaneously(array $network = [], array $steps): array
    {
        $stepsTakenSimultaneously = [];
        $currentNodes = array_filter($network, static fn (string $node) => substr($node, -1) === 'A', ARRAY_FILTER_USE_KEY);

        foreach ($currentNodes as $node => $childNode) {
            $currentNode = $node;
            $stepsTaken = 0;

            while (!(substr($currentNode, -1) === 'Z')) {
                foreach ($steps as $dir) {
                    $stepsTaken += 1;
                    $currentNode = $network[$currentNode][$dir];

                    if (substr($currentNode, -1) === 'Z') {
                        $stepsTakenSimultaneously[] = $stepsTaken;
                        break 2;
                    }
                }
            }
        }

        return $stepsTakenSimultaneously;
    }

    private function gcd(int $a, int $b): int
    {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }

        return $a;
    }

    private function lcm(int $a, int $b): int
    {
        return ($a * $b) / $this->gcd($a, $b);
    }

    /**
     * https://en.wikipedia.org/wiki/Least_common_multiple
     */
    private function findLCM(array $numbers): int
    {
        $lcm = 1;
        foreach ($numbers as $number) {
            $lcm = $this->lcm($lcm, $number);
        }

        return $lcm;
    }
}
