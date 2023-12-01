<?php

namespace Nicojust\Aoc\Year2023\Day01;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'aoc:day:01',
    description: 'Run code.',
    aliases: ['aoc:day1']
)]
class Day01Command extends Command
{
    public final const CASES = [
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
            ->addArgument('env', InputArgument::OPTIONAL, 'test or prod')
            ->addOption(
                'digits',
                'd',
                InputOption::VALUE_NONE,
                'Only use Digits'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $env = $input->getArgument('env');
        $onlyDigits = $input->getOption('digits');

        $filepath = __DIR__ . '/input/prod.txt';
        if ($env) {
            $filepath = sprintf('%s/input/%s.txt', __DIR__, $env);
        }

        $filesystem = new Filesystem();
        if ($filesystem->exists($filepath)) {
            $fileObject = new \SplFileObject($filepath, 'r');

            $complete = 0;
            while (!$fileObject->eof()) {
                $line = $fileObject->fgets();

                $numbers = $this->getValueFromCase($line, $onlyDigits);
                reset($numbers);
                $number = current($numbers) . end($numbers);

                $output->writeln(sprintf('<comment>%s</comment>%s%s<comment>%s</comment>', $line, PHP_EOL, print_r($numbers, true), $number));

                $complete += (int)$number;
            }
            $output->writeln(sprintf('<info>Solution: %s</info>', $complete));

            return Command::SUCCESS;
        }
        $output->writeln(sprintf('<error>File not found at "%s"</error>', $filepath));

        return Command::FAILURE;
    }

    private function getValueFromCase(string $string, bool $onlyDigits = true): array
    {
        $positions = [];
        $cases = [];

        foreach (static::CASES as $key => $case) {
            if ($onlyDigits && !is_int($key)) {
                continue;
            }

            if (str_contains($string, $key)) {
                $pos = strpos($string, $key);
                while ($pos !== false) {
                    $positions[$key][] = $pos;
                    $pos = strpos($string, $key, $pos + 1);
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
                    $numbers[$pos] = static::CASES[$case];
                    $volatilePos = $pos;
                }
            }
        }
        ksort($numbers);

        return $numbers;
    }
}
