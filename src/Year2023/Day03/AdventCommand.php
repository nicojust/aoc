<?php

namespace Nicojust\Aoc\Year2023\Day03;

use Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'aoc:day:03',
    description: 'Run code.',
    aliases: ['aoc:day3']
)]
class AdventCommand extends Command
{
    private array $schematics = [];

    private int $id = 0;

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
            $sumOfNumbersWithAdjacentSymbols = 0;
            $sumOfRatiosWithAdjacentStars = 0;

            foreach ($this->readLinesFromFile($filepath) as $line) {
                $output->writeln(sprintf('<comment>%s</comment>', $line));

                $this->addSchematic($line);
            }
            $hits = $this->checkSchematicPart1();
            $output->writeln(sprintf('<comment>%s</comment>', print_r($hits, true)));
            $sumOfNumbersWithAdjacentSymbols = array_sum($hits);

            $ratios = $this->checkSchematicPart2();
            $output->writeln(sprintf('<comment>%s</comment>', print_r($ratios, true)));
            $sumOfRatiosWithAdjacentStars = array_sum($ratios);

            $output->writeln(sprintf('<info>Solution 1: %s</info>', $sumOfNumbersWithAdjacentSymbols));
            $output->writeln(sprintf('<info>Solution 2: %s</info>', $sumOfRatiosWithAdjacentStars));

            return Command::SUCCESS;
        }
        $output->writeln(sprintf('<error>File not found at "%s"</error>', $filepath));

        return Command::FAILURE;
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

    private function checkSchematicPart1(): array
    {
        $hits = [];

        foreach ($this->schematics as $key => $schema) {
            foreach ($schema['schema'] as $pos => $char) {
                if (!ctype_digit($char) && $char !== '.') {
                    $hasBefore = isset($this->schematics[$key - 1]);
                    $hasAfter = isset($this->schematics[$key + 1]);

                    if ($hasBefore) {
                        if (isset($this->schematics[$key - 1]['schema'][$pos - 1]) && ctype_digit($this->schematics[$key - 1]['schema'][$pos - 1])) {
                            foreach ($this->schematics[$key - 1]['values'] as $value) {
                                if (in_array($pos - 1, $value[1])) {
                                    $hits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                        if (isset($this->schematics[$key - 1]['schema'][$pos]) && ctype_digit($this->schematics[$key - 1]['schema'][$pos])) {
                            foreach ($this->schematics[$key - 1]['values'] as $value) {
                                if (in_array($pos, $value[1])) {
                                    $hits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                        if (isset($this->schematics[$key - 1]['schema'][$pos + 1]) && ctype_digit($this->schematics[$key - 1]['schema'][$pos + 1])) {
                            foreach ($this->schematics[$key - 1]['values'] as $value) {
                                if (in_array($pos + 1, $value[1])) {
                                    $hits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                    }

                    if (isset($this->schematics[$key]['schema'][$pos - 1]) && ctype_digit($this->schematics[$key]['schema'][$pos - 1])) {
                        foreach ($this->schematics[$key]['values'] as $value) {
                            if (in_array($pos - 1, $value[1])) {
                                $hits[$value[2]] = $value[0];
                                break;
                            }
                        }
                    }
                    if (isset($this->schematics[$key]['schema'][$pos]) && ctype_digit($this->schematics[$key]['schema'][$pos])) {
                        foreach ($this->schematics[$key]['values'] as $value) {
                            if (in_array($pos, $value[1])) {
                                $hits[$value[2]] = $value[0];
                                break;
                            }
                        }
                    }
                    if (isset($this->schematics[$key]['schema'][$pos + 1]) && ctype_digit($this->schematics[$key]['schema'][$pos + 1])) {
                        foreach ($this->schematics[$key]['values'] as $value) {
                            if (in_array($pos + 1, $value[1])) {
                                $hits[$value[2]] = $value[0];
                                break;
                            }
                        }
                    }

                    if ($hasAfter) {
                        if (isset($this->schematics[$key + 1]['schema'][$pos - 1]) && ctype_digit($this->schematics[$key + 1]['schema'][$pos - 1])) {
                            foreach ($this->schematics[$key + 1]['values'] as $value) {
                                if (in_array($pos - 1, $value[1])) {
                                    $hits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                        if (isset($this->schematics[$key + 1]['schema'][$pos]) && ctype_digit($this->schematics[$key + 1]['schema'][$pos])) {
                            foreach ($this->schematics[$key + 1]['values'] as $value) {
                                if (in_array($pos, $value[1])) {
                                    $hits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                        if (isset($this->schematics[$key + 1]['schema'][$pos + 1]) && ctype_digit($this->schematics[$key + 1]['schema'][$pos + 1])) {
                            foreach ($this->schematics[$key + 1]['values'] as $value) {
                                if (in_array($pos + 1, $value[1])) {
                                    $hits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $hits;
    }

    private function checkSchematicPart2(): array
    {
        $hits = [];
        $ratios = [];

        foreach ($this->schematics as $key => $schema) {
            foreach ($schema['schema'] as $pos => $char) {
                $volatileHits = [];

                if ($char === '*') {
                    $hasBefore = isset($this->schematics[$key - 1]);
                    $hasAfter = isset($this->schematics[$key + 1]);

                    if ($hasBefore) {
                        if (isset($this->schematics[$key - 1]['schema'][$pos - 1]) && ctype_digit($this->schematics[$key - 1]['schema'][$pos - 1])) {
                            foreach ($this->schematics[$key - 1]['values'] as $value) {
                                if (in_array($pos - 1, $value[1])) {
                                    $volatileHits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                        if (isset($this->schematics[$key - 1]['schema'][$pos]) && ctype_digit($this->schematics[$key - 1]['schema'][$pos])) {
                            foreach ($this->schematics[$key - 1]['values'] as $value) {
                                if (in_array($pos, $value[1])) {
                                    $volatileHits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                        if (isset($this->schematics[$key - 1]['schema'][$pos + 1]) && ctype_digit($this->schematics[$key - 1]['schema'][$pos + 1])) {
                            foreach ($this->schematics[$key - 1]['values'] as $value) {
                                if (in_array($pos + 1, $value[1])) {
                                    $volatileHits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                    }

                    if (isset($this->schematics[$key]['schema'][$pos - 1]) && ctype_digit($this->schematics[$key]['schema'][$pos - 1])) {
                        foreach ($this->schematics[$key]['values'] as $value) {
                            if (in_array($pos - 1, $value[1])) {
                                $volatileHits[$value[2]] = $value[0];
                                break;
                            }
                        }
                    }
                    if (isset($this->schematics[$key]['schema'][$pos]) && ctype_digit($this->schematics[$key]['schema'][$pos])) {
                        foreach ($this->schematics[$key]['values'] as $value) {
                            if (in_array($pos, $value[1])) {
                                $volatileHits[$value[2]] = $value[0];
                                break;
                            }
                        }
                    }
                    if (isset($this->schematics[$key]['schema'][$pos + 1]) && ctype_digit($this->schematics[$key]['schema'][$pos + 1])) {
                        foreach ($this->schematics[$key]['values'] as $value) {
                            if (in_array($pos + 1, $value[1])) {
                                $volatileHits[$value[2]] = $value[0];
                                break;
                            }
                        }
                    }

                    if ($hasAfter) {
                        if (isset($this->schematics[$key + 1]['schema'][$pos - 1]) && ctype_digit($this->schematics[$key + 1]['schema'][$pos - 1])) {
                            foreach ($this->schematics[$key + 1]['values'] as $value) {
                                if (in_array($pos - 1, $value[1])) {
                                    $volatileHits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                        if (isset($this->schematics[$key + 1]['schema'][$pos]) && ctype_digit($this->schematics[$key + 1]['schema'][$pos])) {
                            foreach ($this->schematics[$key + 1]['values'] as $value) {
                                if (in_array($pos, $value[1])) {
                                    $volatileHits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                        if (isset($this->schematics[$key + 1]['schema'][$pos + 1]) && ctype_digit($this->schematics[$key + 1]['schema'][$pos + 1])) {
                            foreach ($this->schematics[$key + 1]['values'] as $value) {
                                if (in_array($pos + 1, $value[1])) {
                                    $volatileHits[$value[2]] = $value[0];
                                    break;
                                }
                            }
                        }
                    }
                }

                if (count($volatileHits) === 2) {
                    $hits[] = $volatileHits;
                }
            }
        }

        foreach ($hits as $hit) {
            $normalizedHit = array_values($hit);

            $ratios[] = $normalizedHit[0] * $normalizedHit[1];
        }

        return $ratios;
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
