<?php

namespace Tests\Console\QAndA;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AddingQuestionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_able_to_quit()
    {
        $this->artisan('qanda:interactive')
            ->expectsQuestion('What would you like to do', 'q')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_should_add_the_given_question_to_database()
    {
        $question = factory(Question::class)->make();
        $answer = factory(Answer::class)->make();

        $this->artisan('qanda:interactive')
            ->expectsQuestion('What would you like to do', 'add')
            ->expectsQuestion('Please provide your question', $question->title)
            ->expectsQuestion('As one question can have multiple answers. Is this multi-choice question', '')
            ->expectsQuestion('Please provide your answer', $answer->value)
            ->expectsQuestion('What would you like to do', 'q')
            ->assertExitCode(0);

        $this->assertDatabaseHas('questions', [
            'title' => $question->title,
            'type' => Question::SINGLE,
        ]);
        $this->assertDatabaseHas('answers', ['value' => $answer->value]);
    }

    /** @test */
    public function it_should_add_the_given_question_with_multiple_answers_to_database()
    {
        $question = factory(Question::class)->make();
        $answers = factory(Answer::class, 3)->make();

        $this->artisan('qanda:interactive')
            ->expectsQuestion('What would you like to do', 'add')
            ->expectsQuestion('Please provide your question', $question->title)
            ->expectsQuestion('As one question can have multiple answers. Is this multi-choice question', 'yes')
            ->expectsQuestion('Please provide your answer', $answers[0]->value)
            ->expectsQuestion('Would you like to add more answer', 'yes')
            ->expectsQuestion('Please provide your answer', $answers[1]->value)
            ->expectsQuestion('Would you like to add more answer', '')
            ->expectsQuestion('What would you like to do', 'q')
            ->assertExitCode(0);

        $this->assertDatabaseHas('questions', [
            'title' => $question->title,
            'type' => Question::MULTI_CHOICE,
        ]);
        $this->assertDatabaseHas('answers', ['value' => $answers[0]->value]);
        $this->assertDatabaseHas('answers', ['value' => $answers[1]->value]);
    }
}
