<?php

namespace Nicojust\Aoc\Year2023\Day04;

use Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'aoc:day:04',
    description: 'Run code.',
    aliases: ['aoc:day4']
)]
class AdventCommand extends Command
{
    private array $lottery = [
        'winners' => [],
        'pool' => [],
    ];

    private array $winners = [];

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
            $scratchCardPoints = 0;

            foreach ($this->readLinesFromFile($filepath) as $key => $line) {
                $output->write(sprintf('<comment>%s</comment>', $line));

                $this->populateLottery($line);
                $this->getMatchingWinners($key);
            }
            $output->writeln('');

            $scratchCardPoints = array_sum(array_map(static fn ($winner) => pow(2, count($winner)) / 2, $this->winners));
            $output->writeln(sprintf('<info>Solution 1: %s</info>', $scratchCardPoints));

            return Command::SUCCESS;
        }
        $output->writeln(sprintf('<error>File not found at "%s"</error>', $filepath));

        return Command::FAILURE;
    }

    private function populateLottery(string $line): void
    {
        $this->lottery['winners'][] = array_filter(explode(' ', trim(explode(':', explode('|', $line)[0])[1])), static fn ($val) => $val !== '');
        $this->lottery['pool'][] = array_filter(explode(' ', trim(explode('|', $line)[1])), static fn ($val) => $val !== '');
    }

    private function getMatchingWinners(int $key): array
    {
        array_walk_recursive($this->lottery['winners'][$key], function ($number) use ($key) {
            if (in_array($number, $this->lottery['pool'][$key])) {
                $this->winners[$key][] = $number;
            }
        });

        return $this->winners;
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
