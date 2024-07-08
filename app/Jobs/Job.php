<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    static function dispatchOrSync(...$arguments)
    {
        return app()->runningInConsole() || app()->environment(['local'])
            ? static::dispatchSync(...$arguments)
            : static::dispatch(...$arguments);
    }
}
