<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Classwork;
use Carbon\Carbon;

class UpdateClassworkStatus extends Command
{
    // Define the command signature and description
    protected $signature = 'classwork:update-status';
    protected $description = 'Update status of classworks based on deadline';


    public function handle()
    {
        // Get all classworks where the deadline has passed and the status is not 'closed'
        $classworks = Classwork::where('deadline', '<', Carbon::now())
            ->where('status', '<>', 'close')
            ->get();

        // Update their status to 'close'
        foreach ($classworks as $classwork) {
            $classwork->status = 'close';
            $classwork->save();
        }

        $this->info('Classwork statuses updated based on deadline');
    }
}