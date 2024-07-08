<?php

namespace App\Http\ApiControllers;

abstract class Controller
{
    protected function responseQueue ($queue, $json = [])
    {
        if (app()->runningInConsole()) {
            $json['queue'] = $queue;
        }
        return response()->json($json);
    }
}
