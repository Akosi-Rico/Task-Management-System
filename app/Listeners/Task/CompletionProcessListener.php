<?php

namespace App\Listeners\Task;

use App\Models\Management\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CompletionProcessListener
{   
    public function handle(object $event): void
    {
       if (!empty($event->parentId)) {
            Task::where("parent_id", $event->parentId)->get()->map(function($item) use (&$event) {
                $item->subTaskCount = $item->where("parent_id", $event->parentId)->count();
                $item->subTaskCompleted = $item->where("parent_id", $event->parentId)
                    ->where("status_id", Task::DONE)
                    ->count();
                if ($item->subTaskCount == $item->subTaskCompleted) {
                    Task::where("id", $event->parentId)->update([
                        'status_id' => Task::DONE
                    ]);
                }
           });
       }
    }
}
