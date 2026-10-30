<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pablo\YahtzeeKata\Yahtzee;

class YahtzeeTest extends TestCase{
    public function test_scores_aces(): void{
        $dice = [1, 1, 2, 4, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(2, $yahtzee->aces($dice));
    }

    public function test_scores_twos(): void{
        $dice = [2, 2, 2, 4, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(6, $yahtzee->twos($dice));
    }

    public function test_scores_threes(): void{
        $dice = [3, 3, 3, 4, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(9, $yahtzee->threes($dice));
    }
    
    public function test_scores_fours(): void{
        $dice = [4, 4, 4, 2, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(12, $yahtzee->fours($dice));
    }

    public function test_scores_fives(): void{
        $dice = [5, 5, 5, 2, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(15, $yahtzee->fives($dice));
    }

    public function test_scores_sixes(): void{
        $dice = [6, 6, 6, 2, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(18, $yahtzee->sixes($dice));
    }

    public function test_scores_three_of_a_kind(): void{
        $dice = [3, 3, 3, 4, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(19, $yahtzee->threeOfAKind($dice));
    }

    public function test_scores_zero_without_three_of_a_kind(): void{
        $dice = [2, 2, 3, 4, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(0, $yahtzee->threeOfAKind($dice));
    }

    public function test_scores_four_of_a_kind(): void{
        $dice = [3, 3, 3, 3, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(18, $yahtzee->fourOfAKind($dice));
    }

    public function test_scores_zero_without_four_of_a_kind(): void{
        $dice = [3, 3, 3, 4, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(0, $yahtzee->fourOfAKind($dice));
        $this->assertEquals(19, $yahtzee->threeOfAKind($dice));
    }

    public function test_scores_full_house(): void{
        $dice = [2, 2, 3, 3, 3];
        $yahtzee = new Yahtzee();
        $this->assertEquals(25, $yahtzee->fullHouse($dice));
    }

    public function test_scores_zero_without_full_house(): void{
        $dice = [2, 2, 2, 3, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(0, $yahtzee->fullHouse($dice));
    }

    public function test_scores_small_straight(): void{
        $dice = [1, 2, 3, 4, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(30, $yahtzee->smallStraight($dice));
    }

    public function test_scores_zero_without_small_straight(): void{
        $dice = [1, 2, 3, 5, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(0, $yahtzee->smallStraight($dice));
    }

    public function test_scores_large_straight(): void{
        $dice = [2, 3, 4, 5, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(40, $yahtzee->largeStraight($dice));
    }

    public function test_scores_zero_without_large_straight(): void{
        $dice = [1, 2, 3, 4, 6];
        $yahtzee = new Yahtzee();
        $this->assertEquals(0, $yahtzee->largeStraight($dice));
    }

    public function test_scores_yahtzee(): void{
        $dice = [4, 4, 4, 4, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(50, $yahtzee->yahtzee($dice));
    }

    public function test_scores_zero_without_yahtzee(): void{
        $dice = [4, 4, 4, 4, 5];
        $yahtzee = new Yahtzee();
        $this->assertEquals(0, $yahtzee->yahtzee($dice));
    }

    public function test_scores_chance(): void{
        $dice = [1, 2, 3, 4, 5];
        $yahtzee = new Yahtzee();
        $this->assertEquals(15, $yahtzee->chance($dice));
    }

    public function test_scores_full_house_with_yahtzee_joker(): void{
        $dice = [4, 4, 4, 4, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(25, $yahtzee->jokerScore('fullHouse', $dice));
    }

    public function test_scores_three_of_a_kind_with_yahtzee_joker(): void{
        $dice = [4, 4, 4, 4, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(20, $yahtzee->jokerScore('threeOfAKind', $dice));
    }

    public function test_scores_four_of_a_kind_with_yahtzee_joker(): void{
        $dice = [4, 4, 4, 4, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(20, $yahtzee->jokerScore('fourOfAKind', $dice));
    }

    public function test_scores_small_straight_with_yahtzee_joker(): void{
        $dice = [4, 4, 4, 4, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(30, $yahtzee->jokerScore('smallStraight', $dice));
    }

    public function test_scores_large_straight_with_yahtzee_joker(): void{
        $dice = [4, 4, 4, 4, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(40, $yahtzee->jokerScore('largeStraight', $dice));
    }

    public function test_scores_chance_with_yahtzee_joker(): void{
        $dice = [4, 4, 4, 4, 4];
        $yahtzee = new Yahtzee();
        $this->assertEquals(20, $yahtzee->jokerScore('chance', $dice));
    }

    public function test_rejects_unknown_joker_category(): void{
        $yahtzee = new Yahtzee();
        $this->expectException(\InvalidArgumentException::class);
        $yahtzee->jokerScore('unknown', [4, 4, 4, 4, 4]);
    }
}