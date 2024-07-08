<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateNumber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $model;
    public $strset;
    public $digits;

    public function __construct($model, $strset, $digits = 6)
    {
        $this->model = $model;
        $this->digits = $digits;
        $this->strset = $strset;
    }

    public function handle()
    {
        $model = $this->model;
        $numberset = (string) str($this->strset)->replace("{number}", "");

        $number = $model->getNumberNextQuery($numberset, $this->digits);
        $number = (string) str($this->strset)->replace("{number}", $number);

        $model->number = $number;
        $model->save();
    }
}
