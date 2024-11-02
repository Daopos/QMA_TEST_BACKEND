<?php

namespace App\Console\Commands;

use App\Models\Test;
use Illuminate\Console\Command;

class updateTestStatus extends Command
{

    protected $signature = 'tests:update-status';
    protected $description = 'Update test status to closed if deadline has passed';

    public function handle()
    {


        // Update the status to 'closed' where the deadline has passed
        Test::where('deadline', '<=', now())
            ->where('status', '!=', 'closed') // Only update if not already closed
            ->update(['status' => 'closed']);

        $this->info('Test statuses updated successfully.');
    }
}