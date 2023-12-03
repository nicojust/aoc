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
            $solution = 0;

            foreach ($this->readLinesFromFile($filepath) as $line) {
                $output->writeln(sprintf('<comment>%s</comment>', $line));
            }

            $output->writeln(sprintf('<info>Solution: %s</info>', $solution));

            return Command::SUCCESS;
        }
        $output->writeln(sprintf('<error>File not found at "%s"</error>', $filepath));

        return Command::FAILURE;
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
