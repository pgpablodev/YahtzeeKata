<?php

declare(strict_types=1);

namespace Pablo\YahtzeeKata;

class Yahtzee{
    public function aces(array $dice): int{
        return $this->scoreNumber($dice, 1);
    }

    public function twos(array $dice): int{
        return $this->scoreNumber($dice, 2);
    }

    public function threes(array $dice): int{
        return $this->scoreNumber($dice, 3);
    }

    public function fours(array $dice): int{
        return $this->scoreNumber($dice, 4);
    }

    public function fives(array $dice): int{
        return $this->scoreNumber($dice, 5);
    }

    public function sixes(array $dice): int{
        return $this->scoreNumber($dice, 6);
    }

    private function scoreNumber(array $dice, int $number): int{
        return $number * count(array_filter(
            $dice,
            fn (int $die) => $die === $number
        ));
    }

    public function threeOfAKind(array $dice): int{
        $counts = array_count_values($dice);

        foreach($counts as $count){
            if($count >= 3){
                return array_sum($dice);
            }
        }

        return 0;
    }

    public function fourOfAKind(array $dice): int{
        $counts = array_count_values($dice);

        foreach($counts as $count){
            if($count >= 4){
                return array_sum($dice);
            }
        }

        return 0;
    }

    public function fullHouse(array $dice): int{
        $counts = array_count_values($dice);
        $frequencies = array_values($counts);

        sort($frequencies);

        return $frequencies === [2, 3] ? 25 : 0;
    }

    public function smallStraight(array $dice): int{
        $uniqueDice = array_unique($dice);
        sort($uniqueDice);

        $sequences = [
            [1, 2, 3, 4],
            [2, 3, 4, 5],
            [3, 4, 5, 6],
        ];

        foreach($sequences as $sequence){
            if(count(array_intersect($sequence, $uniqueDice)) === 4){
                return 30;
            }
        }

        return 0;
    }

    public function largeStraight(array $dice): int{
        $uniqueDice = array_unique($dice);
        sort($uniqueDice);

        $sequences = [
            [1, 2, 3, 4, 5],
            [2, 3, 4, 5, 6],
        ];

        foreach($sequences as $sequence){
            if($uniqueDice === $sequence){
                return 40;
            }
        }

        return 0;
    }

    public function yahtzee(array $dice): int{
        return count(array_unique($dice)) === 1 ? 50 : 0;
    }

    public function chance(array $dice): int{
        return array_sum($dice);
    }

    public function jokerScore(string $category, array $dice): int{
        return match($category){
            'fullHouse' => 25,
            'smallStraight' => 30,
            'largeStraight' => 40,
            'threeOfAKind', 'fourOfAKind', 'chance' => array_sum($dice),
            default => throw new \InvalidArgumentException('Unknown category'),
        };
    }
}