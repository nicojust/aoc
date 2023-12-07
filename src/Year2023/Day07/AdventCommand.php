<?php

declare(strict_types=1);

namespace NicoJust\AoC\Year2023\Day07;

use NicoJust\AoC\Util;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'aoc:day:07',
    description: 'Day 7: Camel Cards',
    aliases: ['aoc:day6']
)]
class AdventCommand extends Command
{
    /**
     * @var int[]
     */
    private const array CARDS = [
        'A' => 14,
        'K' => 13,
        'Q' => 12,
        'J' => 11,
        'T' => 10,
        '9' => 9,
        '8' => 8,
        '7' => 7,
        '6' => 6,
        '5' => 5,
        '4' => 4,
        '3' => 3,
        '2' => 2,
    ];

    private array $hands = [];
    private array $wildcardHands = [];

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

        $points = 0;
        $wildcardPoints = 0;
        foreach (Util::readLine(Util::getFilePath($input, __DIR__)) as $line) {
            $output->write(sprintf('<comment>%s</comment>', $line));

            $this->hands[] = $this->parseHands($line);
            $this->wildcardHands[] = $this->parseHands($line, true);
        }
        $output->writeln('');

        $sortedHands = $this->sortHands($this->hands);
        $points = array_sum(array_map(
            static fn (array $hand, int $multiplier)  => ((int)$hand[1] * ($multiplier + 1)),
            $sortedHands,
            array_keys(array_values($sortedHands))
        ));

        $sortedWildcardHands = $this->sortHands($this->wildcardHands, true);
        $wildcardPoints = array_sum(array_map(
            static fn (array $hand, int $multiplier)  => ((int)$hand[1] * ($multiplier + 1)),
            $sortedWildcardHands,
            array_keys(array_values($sortedWildcardHands))
        ));

        $output->writeln(sprintf('<info>Solution 1: %d</info>', $points));
        $output->writeln(sprintf('<info>Solution 2: %d</info>', $wildcardPoints));


        return Command::SUCCESS;
    }

    private function parseHands(string $line, bool $wildcard = false): array
    {
        $parts = array_map('trim', explode(' ', $line));

        $cards = [];
        $bid = (int)$parts[1];
        foreach (str_split($parts[0]) as $card) {
            $cards[] = $this->getCardPointValue($card, $wildcard);
        }

        return [$cards, $bid];
    }

    private function getCardPointValue(string $card, bool $wildcard = false): int
    {
        return $wildcard && $card === 'J' ? 1 : self::CARDS[$card];
    }

    /**
     * Part 1   Part 2
     *
     * 32T3K    32T3K
     * KTJJT    KK677
     * KK677    T55J5
     * T55J5    QQQJA
     * QQQJA    KTJJT
     */
    private function scoreHand(array $hand, bool $wildcard = false): int
    {
        $cardPairs = array_count_values($hand[0]);
        $max = max($cardPairs);
        if ($wildcard && array_key_exists(1, $cardPairs)) {
            $wildcards = $cardPairs[1];
            unset($cardPairs[1]);

            $max = $wildcards;
            if (!empty($cardPairs)) {
                $max += max($cardPairs);
            }
        }

        // High card
        $score = 0;
        switch (true) {
            case $max === 5:
                // Five of a kind
                $score = 6;
                break;
            case $max === 4:
                // Four of a kind
                $score = 5;
                break;
            case count($cardPairs) === 2:
                // Full house
                $score = 4;
                break;
            case $max === 3:
                // Three of a kind
                $score = 3;
                break;
            case count($cardPairs) === 3:
                // Two pair
                $score = 2;
                break;
            case $max === 2:
                // One pair
                $score = 1;
                break;
        }

        return $score;
    }

    private function sortHands(array $hands = [], bool $wildcard = false): array
    {
        uasort($hands, function (array $l, array $r) use ($wildcard) {
            $lp = $this->scoreHand($l, $wildcard);
            $rp = $this->scoreHand($r, $wildcard);

            // tiebreaker
            if ($lp === $rp) {
                foreach (range(0, count($l[0]) - 1) as $key) {
                    if ($l[0][$key] !== $r[0][$key]) {
                        return $l[0][$key] <=> $r[0][$key];
                    }
                }
            }

            return $lp <=> $rp;
        });

        return $hands;
    }
}
