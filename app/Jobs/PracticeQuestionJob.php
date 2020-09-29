<?php

namespace App\Jobs;

use App\Console\Commands\QAndAInteractiveCommand;
use App\Models\Question;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class PracticeQuestionJob
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
     * @return bool
     */
    public function handle(Question $question)
    {
        $questions = $question->all()
            ->map(function ($question) {
                if ($question->completed_at) {
                    $question->title = "⭐ [complete] {$question->title}";
                }

                return $question;
            });

        $selectedQuestion = $this->console->choice(
            'Which question you would like to practice',
            $questions->pluck('title', 'id')->toArray()
        );
        $feedback = $this->console->ask('Please provide your answer');
        /** @var Question $question */
        $question = $question->where('title', $selectedQuestion)
            ->whereHas(
                'answers',
                function ($answer) use ($feedback) {
                    return $answer->where('value', $feedback);
                }
            )->first();

        if ($question) {
            $this->console->info(' ✅ Your answer is correct!');
            $question->markCompleted();

            return true;
        }

        $this->console->warn('Incorrect answer provided.');

        return false;
    }
}
