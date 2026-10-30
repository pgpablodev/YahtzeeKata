<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pablo\YahtzeeKata\Scorecard;
use Pablo\YahtzeeKata\Yahtzee;

class ScorecardTest extends TestCase{
    public function test_records_a_category_score(): void{
        $scorecard = new Scorecard();
        $scorecard->record('aces', 3);
        $this->assertEquals(3, $scorecard->score('aces'));
    }

    public function test_cannot_record_a_category_twice(): void{
        $scorecard = new Scorecard();
        $scorecard->record('aces', 3);
        $this->expectException(\DomainException::class);
        $scorecard->record('aces', 5);
    }

    public function test_returns_available_categories(): void{
        $scorecard = new Scorecard();

        $scorecard->record('aces', 3);
        $scorecard->record('twos', 6);

        $this->assertEquals([
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
        ], $scorecard->availableCategories());
    }

    public function test_calculates_upper_section_total(): void{
        $scorecard = new Scorecard();

        $scorecard->record('aces', 3);
        $scorecard->record('twos', 6);
        $scorecard->record('threes', 9);
        $scorecard->record('fours', 12);
        $scorecard->record('fives', 15);
        $scorecard->record('sixes', 18);
        $scorecard->record('threeOfAKind', 20);

        $this->assertEquals(63, $scorecard->upperSectionTotal());
    }

    public function test_awards_upper_section_bonus_when_total_reaches_63(): void{
        $scorecard = new Scorecard();

        $scorecard->record('aces', 3);
        $scorecard->record('twos', 6);
        $scorecard->record('threes', 9);
        $scorecard->record('fours', 12);
        $scorecard->record('fives', 15);
        $scorecard->record('sixes', 18);

        $this->assertEquals(35, $scorecard->upperSectionBonus());
    }

    public function test_does_not_award_upper_section_bonus_below_63(): void{
        $scorecard = new Scorecard();

        $scorecard->record('aces', 3);
        $scorecard->record('twos', 6);
        $scorecard->record('threes', 9);
        $scorecard->record('fours', 12);
        $scorecard->record('fives', 15);
        $scorecard->record('sixes', 17);

        $this->assertEquals(0, $scorecard->upperSectionBonus());
    }

    public function test_calculates_lower_section_total(): void{
        $scorecard = new Scorecard();

        $scorecard->record('aces', 3);
        $scorecard->record('twos', 6);
        $scorecard->record('threeOfAKind', 20);
        $scorecard->record('fourOfAKind', 25);
        $scorecard->record('fullHouse', 25);
        $scorecard->record('smallStraight', 30);
        $scorecard->record('largeStraight', 40);
        $scorecard->record('yahtzee', 50);
        $scorecard->record('chance', 22);

        $this->assertEquals(212, $scorecard->lowerSectionTotal());
    }

    public function test_calculates_total_score(): void{
        $scorecard = new Scorecard();

        $scorecard->record('aces', 3);
        $scorecard->record('twos', 6);
        $scorecard->record('threes', 9);
        $scorecard->record('fours', 12);
        $scorecard->record('fives', 15);
        $scorecard->record('sixes', 18);

        $scorecard->record('threeOfAKind', 20);
        $scorecard->record('fourOfAKind', 25);
        $scorecard->record('fullHouse', 25);
        $scorecard->record('smallStraight', 30);
        $scorecard->record('largeStraight', 40);
        $scorecard->record('yahtzee', 50);
        $scorecard->record('chance', 22);

        $this->assertEquals(310, $scorecard->totalScore());
    }

    public function test_scorecard_is_complete_when_all_categories_are_scored(): void{
        $scorecard = new Scorecard();

        $categories = [
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

        foreach($categories as $category){
            $scorecard->record($category, 0);
        }

        $this->assertTrue($scorecard->isComplete());
    }

    public function test_scorecard_is_not_complete_when_categories_remain(): void{
        $scorecard = new Scorecard();
        $scorecard->record('aces', 3);
        $this->assertFalse($scorecard->isComplete());
    }

    public function test_awards_100_points_for_a_second_yahtzee(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 50);
        $scorecard->recordYahtzeeBonus();

        $this->assertEquals(100, $scorecard->yahtzeeBonus());
    }

    public function test_accumulates_multiple_yahtzee_bonuses(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 50);
        $scorecard->recordYahtzeeBonus();
        $scorecard->recordYahtzeeBonus();
        $scorecard->recordYahtzeeBonus();

        $this->assertEquals(300, $scorecard->yahtzeeBonus());
    }

    public function test_does_not_award_bonus_when_first_yahtzee_scored_zero(): void{
        $scorecard = new Scorecard();
        $scorecard->record('yahtzee', 0);
        $this->expectException(\DomainException::class);
        $scorecard->recordYahtzeeBonus();
    }

    public function test_includes_yahtzee_bonus_in_total_score(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 50);
        $scorecard->recordYahtzeeBonus();

        $this->assertEquals(150, $scorecard->totalScore());
    }

    public function test_total_score_without_yahtzee_bonus(): void{
        $scorecard = new Scorecard();
        $scorecard->record('yahtzee', 50);
        $this->assertEquals(50, $scorecard->totalScore());
    }

    public function test_allows_joker_when_matching_upper_category_is_scored(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 50);
        $scorecard->record('fours', 20);

        $this->assertTrue(
            $scorecard->canUseJoker([4, 4, 4, 4, 4])
        );
    }

    public function test_cannot_use_joker_when_first_yahtzee_scored_zero(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 0);
        $scorecard->record('fours', 20);

        $this->assertFalse(
            $scorecard->canUseJoker([4, 4, 4, 4, 4])
        );
    }

    public function test_cannot_use_joker_without_yahtzee_roll(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 50);
        $scorecard->record('fours', 20);

        $this->assertFalse(
            $scorecard->canUseJoker([4, 4, 4, 4, 5])
        );
    }

    public function test_cannot_use_joker_when_matching_upper_category_is_available(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 50);

        $this->assertFalse(
            $scorecard->canUseJoker([4, 4, 4, 4, 4])
        );
    }

    public function test_allows_upper_category_when_no_lower_categories_are_available(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 50);
        $scorecard->record('fours', 20);

        $lowerCategories = [
            'threeOfAKind',
            'fourOfAKind',
            'fullHouse',
            'smallStraight',
            'largeStraight',
            'chance',
        ];

        foreach($lowerCategories as $category){
            $scorecard->record($category, 0);
        }

        $this->assertTrue(
            $scorecard->canUseJoker([4, 4, 4, 4, 4])
        );
    }

    public function test_returns_no_available_lower_categories(): void{
        $scorecard = new Scorecard();

        $scorecard->record('yahtzee', 50);
        $scorecard->record('fours', 20);

        foreach([
            'threeOfAKind',
            'fourOfAKind',
            'fullHouse',
            'smallStraight',
            'largeStraight',
            'chance',
        ] as $category){
            $scorecard->record($category, 0);
        }

        $this->assertSame([], $scorecard->availableLowerCategories());
    }

    public function test_records_yahtzee_bonus_and_joker_score(): void{
        $scorecard = new Scorecard();
        $yahtzee = new Yahtzee();

        $scorecard->record('yahtzee', 50);
        $scorecard->record('fours', 20);

        $dice = [4, 4, 4, 4, 4];

        $score = $yahtzee->jokerScore('fullHouse', $dice);

        $scorecard->recordYahtzeeBonus();
        $scorecard->record('fullHouse', $score);

        $this->assertEquals(25, $scorecard->score('fullHouse'));
        $this->assertEquals(100, $scorecard->yahtzeeBonus());
        $this->assertEquals(195, $scorecard->totalScore());
    }
}