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
    protected Request $request;

    static function dispatchOrSync(...$arguments)
    {
        if (app()->runningInConsole()) {
            return static::dispatch(...$arguments)->onConnection('sync');
        }

        return static::dispatch(...$arguments);

    }

    protected function setQueueRequest ($request)
    {
        $this->request = new Request($request);
        $this->qid = $this->request->get('qid') ?: str()->uuid();
    }
}
