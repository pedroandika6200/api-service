<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected string $qid;

    static function dispatchOrSync(...$arguments)
    {
        if (app()->runningInConsole()) {
            return static::dispatchSync(...$arguments);
        }
        return static::dispatch(...$arguments);
    }

    protected function generateKey (Request $request = new Request())
    {
        $this->qid = $request->get('qid') ?: str()->uuid();
    }
}
