<?php

declare(strict_types=1);

namespace NicoJust\AoC\Year2023\Day10;

use NicoJust\AoC\Year2023\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:year:23:day:10',
    description: 'Day 10: Pipe Maze (https://adventofcode.com/2023/day/10)',
    aliases: ['aoc:year:23:day:10', 'aoc:year:1:day:10'],
)]
class AdventCommand extends Command
{
    private const NORTH = [-1, 0];
    private const SOUTH = [1, 0];
    private const EAST = [0, 1];
    private const WEST = [0, -1];

    private const DIRECTIONS = [
        self::NORTH,
        self::SOUTH,
        self::EAST,
        self::WEST,
    ];

    private const TILES = [
        '|' => [self::NORTH, self::SOUTH],
        '-' => [self::EAST, self::WEST],
        'L' => [self::NORTH, self::EAST],
        'J' => [self::NORTH, self::WEST],
        '7' => [self::SOUTH, self::WEST],
        'F' => [self::SOUTH, self::EAST],
        '.' => false,
        'S' => true,
    ];

    private const TILES_UNICODE = [
        '|' => '│',
        '-' => '─',
        'L' => '└',
        'J' => '┘',
        '7' => '┐',
        'F' => '┌',
    ];

    private array $grid = [];
    private array $startTile = [];
    private array $startCoords = [];

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

        $farthestSteps = 0;
        $tilesWithinLoop = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $line) {
            $output->write(sprintf('%s', $this->parseGridUnicode($line)));

            $this->createGrid($line);
        }
        $output->writeln('');

        [$this->startTile, $this->startCoords] = $this->createStartTile($this->grid);
        [$y, $x] = $this->startCoords;
        $this->grid[$y][$x] = $this->startTile;

        // holy mother of god
        ini_set('xdebug.max_nesting_level', -1);

        $visited = $this->walkLoop($this->grid, $this->startCoords, []);
        $farthestSteps = count($visited) / 2;

        $output->writeln(sprintf('<info>Solution 1: %d</info>', $farthestSteps));
        $output->writeln(sprintf('<info>Solution 2: %d</info>', $tilesWithinLoop));

        return Command::SUCCESS;
    }

    private function createGrid(string $line): void
    {
        $tiles = str_split(trim($line));

        $pipes = [];
        foreach ($tiles as $key => $tile) {
            $pipes[$key] = self::TILES[$tile];
        }

        $this->grid[] = $pipes;
    }

    private function parseGridUnicode(string $line): string
    {
        $search = array_keys(self::TILES_UNICODE);
        $replace = array_map(static fn(string $u) => sprintf('<comment>%s</comment>', $u), array_values(self::TILES_UNICODE));

        return str_replace($search, $replace, $line);
    }

    private function createStartTile(array $grid = []): array
    {
        $connections = [];
        $tileCoords = [];

        for ($y = 0; $y < count($grid); $y++) {
            for ($x = 0; $x < count($grid[$y]); $x++) {
                $tile = $grid[$y][$x];

                if ($tile === true) {
                    $tileCoords = [$y, $x];

                    foreach (self::DIRECTIONS as $direction) {
                        [$a, $b] = $direction;

                        $ya = $y + ($a);
                        $xb = $x + ($b);

                        if (!isset($grid[$ya][$xb])) {
                            continue;
                        }

                        $loopTile = $grid[$ya][$xb];
                        if (is_array($loopTile)) {
                            foreach ($loopTile as $lt) {
                                [$c, $d] = $lt;

                                $connectTile = $grid[$ya + ($c)][$xb + ($d)];
                                if (true === $connectTile) {
                                    $connections[] = [$c, $d];
                                    if (count($connections) === 2) {
                                        break 4;
                                    } else {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($connections as &$connection) {
            foreach ($connection as $key => $dir) {
                $connection[$key] = $dir * -1;
            }
        }

        return [$connections, $tileCoords];
    }

    private function walkLoop(array $grid, array $coords, array $visited): array
    {
        [$y, $x] = $coords;

        while ($this->startCoords !== $coords || count($visited) < count($grid)) {
            $tile = $grid[$y][$x];
            if (!is_array($tile)) {
                continue;
            }
            $visited[] = [$y, $x];

            foreach ($tile as $dir) {
                [$a, $b] = $dir;

                $ya = $y + ($a);
                $xb = $x + ($b);

                if (!isset($grid[$ya][$xb]) || in_array([$ya, $xb], $visited, true)) {
                    continue;
                }

                return $this->walkLoop($this->grid, [$ya, $xb], $visited);
            }

            return $this->walkLoop($this->grid, $this->startCoords, $visited);
        }

        return $visited;
    }
}
