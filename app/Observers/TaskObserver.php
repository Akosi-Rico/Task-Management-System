<?php

namespace App\Observers;

use App\Events\Task\CompletionProcess;
use App\Models\Management\Task;
class TaskObserver
{
    public function updated(Task $model): void
    {
        CompletionProcess::dispatch($model->parent_id);
    }
}
