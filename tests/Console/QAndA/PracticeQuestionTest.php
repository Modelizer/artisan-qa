<?php

namespace Tests\Console\QAndA;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PracticeQuestionTest extends TestCase
{
    /** @test */
    public function it_should_affirm_right_answer()
    {
        $question = factory(Question::class)->create();

        $this->artisan('qanda:interactive')
            ->expectsQuestion('What would you like to do', 'practice')
            ->expectsQuestion('Which question you would like to practice', $question->id)
            ->expectsQuestion('Please provide your answer', $question->answers->first()->value)
            ->expectsQuestion('What would you like to do', 'q')
            ->assertExitCode(0);

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
        ]);
    }
}
