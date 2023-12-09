<?php

declare(strict_types=1);

namespace NicoJust\AoC\Year2023\Day09;

use NicoJust\AoC\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:year:23:day:09',
    description: 'Day 9: Mirage Maintenance (https://adventofcode.com/2023/day/9)',
    aliases: ['aoc:year:23:day:9', 'aoc:year:1:day:9'],
)]
class AdventCommand extends Command
{
    private array $extrapolatedNumbers = [];
    private array $backwardsExtrapolatedNumbers = [];

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

        $sumOfExtrapolatedNumbers = 0;
        $sumOfBackwardsExtrapolatedNumbers = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $line) {
            $output->write(sprintf('<comment>%s</comment>', $line));

            $numbers = $this->parseSequence($line);
            $this->extrapolatedNumbers[] = $this->extrapolate($numbers);
            $this->backwardsExtrapolatedNumbers[] = $this->extrapolate(array_reverse($numbers));
        }
        $output->writeln('');

        $sumOfExtrapolatedNumbers = array_sum($this->extrapolatedNumbers);
        $sumOfBackwardsExtrapolatedNumbers = array_sum($this->backwardsExtrapolatedNumbers);

        $output->writeln(sprintf('<info>Solution 1: %d</info>', $sumOfExtrapolatedNumbers));
        $output->writeln(sprintf('<info>Solution 1: %d</info>', $sumOfBackwardsExtrapolatedNumbers));

        return Command::SUCCESS;
    }

    private function parseSequence(string $line): array
    {
        return array_map('intval', explode(' ', $line));
    }

    private function extrapolate(array $numbers): int
    {
        $rows = [];
        for ($i = 1; $i < count($numbers); $i++) {
            $rows[] = $numbers[$i] - $numbers[$i - 1];
        }

        $lastNumber = $numbers[array_key_last($numbers)];
        $isEndOfSequence = array_sum($rows) === 0 && count(array_count_values($rows)) === 1;

        return $isEndOfSequence ? $lastNumber : $lastNumber + $this->extrapolate($rows);
    }
}
