<?php

declare(strict_types=1);

namespace Nicojust\Aoc\Year2023\Day06;

use Nicojust\Aoc\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:day:06',
    description: 'Day 6: Wait For It',
    aliases: ['aoc:day6']
)]
class AdventCommand extends Command
{
    private array $races = [];

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

        $multipliedRaceTimes = 0;
        $waysToBeatRecord = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $key => $line) {
            $output->write(sprintf('<comment>%s</comment>', $line));

            $this->parseRaces($line, $key);
        }
        $output->writeln('');

        $races = array_combine($this->races[0], $this->races[1]);
        $multipliedRaceTimes = array_reduce($this->calulateTimes($races), static fn ($acc, $times) => $acc * count($times), 1);

        $race = [(int)implode('', $this->races[0]) => (int)implode('', $this->races[1])];
        $waysToBeatRecord = count($this->calulateTime($race));

        $output->writeln(sprintf('<info>Solution 1: %d</info>', $multipliedRaceTimes));
        $output->writeln(sprintf('<info>Solution 2: %d</info>', $waysToBeatRecord));

        return Command::SUCCESS;
    }

    private function parseRaces(string $line, int $key): void
    {
        $replace = str_contains($line, 'Time:') ? 'Time:' : 'Distance:';
        $this->races[$key] = array_values(
            array_map('intval', array_filter(explode(' ', str_replace($replace, '', $line)), static fn ($time) => $time !== ''))
        );
    }

    private function calulateTimes(array $races = []): array
    {
        $times = [];

        foreach ($races as $time => $distance) {
            for ($timeHeld = $time; $timeHeld >= 0; $timeHeld--) {
                if (($time - $timeHeld) * $timeHeld > $distance) {
                    $times[$time][] = $timeHeld;
                }
            }
        }

        return $times;
    }

    private function calulateTime(array $race = []): array
    {
        $times = [];

        foreach ($race as $time => $distance) {
            for ($timeHeld = $time; $timeHeld >= 0; $timeHeld--) {
                if (($time - $timeHeld) * $timeHeld > $distance) {
                    $times[] = $timeHeld;
                }
            }
        }

        return $times;
    }
}
