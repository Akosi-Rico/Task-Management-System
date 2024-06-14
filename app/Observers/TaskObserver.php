<?php

namespace App\Observers;

use App\Models\Management\Task;
class TaskObserver
{
    public function created(Task $model): void
    {
        info($model);
    }
}
