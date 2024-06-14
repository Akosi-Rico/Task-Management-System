<?php

namespace App\Providers;

use App\Events\Task\CompletionProcess;
use App\Listeners\Task\CompletionProcessListener;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(
           CompletionProcess::class,
           CompletionProcessListener::class
        );
    }
}
