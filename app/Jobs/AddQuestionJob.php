<?php

namespace App\Jobs;

use App\Console\Commands\QAndAInteractiveCommand;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class AddQuestionJob
{
    use Dispatchable;

    /** @var \App\Console\Commands\QAndAInteractiveCommand $console */
    protected $console;

    /**
     * @param \App\Console\Commands\QAndAInteractiveCommand $console
     */
    public function __construct(QAndAInteractiveCommand $console)
    {
        $this->console = $console;
    }

    /**
     * @param \App\Models\Question $question
     * @param \App\Models\Answer $answer
     * @return bool
     */
    public function handle(Question $question, Answer $answer)
    {
        $this->console->warn('Question and answers will only be save to database when you complete it fully.');
        $question->fill([
            'title' => $this->console->ask('Please provide your question'),
            'type' => Question::SINGLE,
        ]);

        if ($this->console->confirm('As one question can have multiple answers. Is this multi-choice question')) {
            $this->saveAllAnswers($question);

            return true;
        }

        $answerValue = $this->askForAnswer();
        $question->save();
        $answer->create([
            'question_id' => $question->id,
            'value' => $answerValue
        ]);

        return true;
    }

    /**
     * @param array $answers
     * @return array|mixed
     */
    public function askForAnswers($answers = [])
    {
        $answers[] = $this->askForAnswer();

        if ($this->console->confirm('Would you like to add more answer')) {
            return $this->askForAnswers($answers);
        }

        return $answers;
    }

    /**
     * @return mixed
     */
    public function askForAnswer()
    {
        return $this->console->ask('Please provide your answer');
    }

    /**
     * @param \App\Models\Question $question
     */
    protected function saveAllAnswers(Question $question)
    {
        DB::transaction(function () use (&$question) {
            collect($this->askForAnswers())
                ->tap(function () use (&$question) {
                    $question->type = Question::MULTI_CHOICE;
                    $question->save();
                })
                ->map(function ($answer) use ($question) {
                    $question->answers()->create([
                        'value' => $answer,
                    ]);
                });
        });
    }
}
