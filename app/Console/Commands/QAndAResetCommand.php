<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;

class QAndAResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resetting all completed at';

    /**
     * @param \Illuminate\Database\Connection $database
     */
    public function handle(Connection $database)
    {
        $database->table('questions')->update([
            'completed_at' => null,
        ]);
    }
}
