<?php

namespace App\Console\Commands;

use App\Jobs\AddQuestionJob;
use App\Jobs\PracticeQuestionJob;
use App\Models\Question;
use Illuminate\Console\Command;

class QAndAInteractiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs an interactive command line based Q And A system.';

    /**
     * @var string[] $userOptions
     */
    protected $userOptions = [
        'add' => 'Add a question',
        'practice' => 'Practice the questions',
        'view' => 'View your progress',
        'quit' => 'Quit',
    ];

    /**
     * @return bool
     */
    public function handle()
    {
        switch ($option = $this->choice('What would you like to do', $this->userOptions)) {
            case $option == $this->userOptions['add'] || $option == 'add':
                AddQuestionJob::dispatch($this);

                return $this->handle();
            case $option == $this->userOptions['practice'] || $option == 'practice':
                PracticeQuestionJob::dispatch($this);

                return $this->handle();
            case $option == $this->userOptions['view'] || $option == 'view':
                $this->userProgress();

                return $this->handle();
            case $option == $this->userOptions['quit'] || $option == 'quit':

                return true;
        }
    }

    /**
     * @return void
     */
    public function userProgress()
    {
        /** @var \Illuminate\Database\Eloquent\Collection $questions */
        $questions = Question::get(['title', 'completed_at']);

        $this->table(['Title', 'Completed At'], $questions->toArray());
    }
}
