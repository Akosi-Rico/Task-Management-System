<?php

namespace App\Console\Commands\Task;

use App\Models\Management\Task;
use Illuminate\Console\Command;

class DestroyTask extends Command
{
    protected $signature = 'task:destroy-task';

    protected $description = 'Destroy task with status of Archive within 30 days';

    public function handle()
    {
        Task::where("status_id", Task::ARCHIVE)->get()->map(function($item) {
            $diff = now()->parse($item->updated_at->format("Y-m-d"))->diffInDays(now()->format("Y-m-d"));
            if ($diff > 30) {
                Task::where("id", $item->id)->delete();
            }
        });
    }
}
