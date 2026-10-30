<?php

declare(strict_types=1);

namespace Pablo\YahtzeeKata;

class Scorecard{
    private array $scores = [];
    private int $yahtzeeBonus = 0;
    private const CATEGORIES = [
        'aces',
        'twos',
        'threes',
        'fours',
        'fives',
        'sixes',
        'threeOfAKind',
        'fourOfAKind',
        'fullHouse',
        'smallStraight',
        'largeStraight',
        'yahtzee',
        'chance',
    ];

    public function score(string $category): ?int{
        return $this->scores[$category] ?? null;
    }

    public function record(string $category, int $score): void{
        if(isset($this->scores[$category])){
            throw new \DomainException('Category already scored');
        }

        $this->scores[$category] = $score;
    }

    public function availableCategories(): array{
        return array_values(array_diff(
            self::CATEGORIES,
            array_keys($this->scores)
        ));
    }

    public function upperSectionTotal(): int{
        $upperCategories = [
            'aces',
            'twos',
            'threes',
            'fours',
            'fives',
            'sixes',
        ];

        $upperScores = array_intersect_key(
            $this->scores,
            array_flip($upperCategories)
        );

        return array_sum($upperScores);
    }

    public function upperSectionBonus(): int{
        return $this->upperSectionTotal() >= 63 ? 35 : 0;
    }

    public function lowerSectionTotal(): int{
        $lowerCategories = [
            'threeOfAKind',
            'fourOfAKind',
            'fullHouse',
            'smallStraight',
            'largeStraight',
            'yahtzee',
            'chance',
        ];

        $lowerScores = array_intersect_key(
            $this->scores,
            array_flip($lowerCategories)
        );

        return array_sum($lowerScores);
    }

    public function totalScore(): int{
        return $this->upperSectionTotal() + $this->upperSectionBonus() + $this->lowerSectionTotal() + $this->yahtzeeBonus();
    }

    public function isComplete(): bool{
        return empty($this->availableCategories());
    }

    public function recordYahtzeeBonus(): void{
        if($this->score('yahtzee') !== 50){
            throw new \DomainException('Yahtzee bonus not available');
        }
        $this->yahtzeeBonus += 100;
    }

    public function yahtzeeBonus(): int{
        return $this->yahtzeeBonus;
    }

    public function canUseJoker(array $dice): bool{
        if($this->score('yahtzee') !== 50){
            return false;
        }

        if(count(array_unique($dice)) !== 1){
            return false;
        }

        $matchingCategory = [
            1 => 'aces',
            2 => 'twos',
            3 => 'threes',
            4 => 'fours',
            5 => 'fives',
            6 => 'sixes',
        ][$dice[0]];

        return $this->score($matchingCategory) !== null;
    }

    public function availableLowerCategories(): array{
        $lowerCategories = [
            'threeOfAKind',
            'fourOfAKind',
            'fullHouse',
            'smallStraight',
            'largeStraight',
            'yahtzee',
            'chance',
        ];

        return array_values(array_intersect(
            $this->availableCategories(),
            $lowerCategories
        ));
    }
}