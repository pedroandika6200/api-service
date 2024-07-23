<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Recordset\Concerns\HasFilterable;

class Lockerable extends Model
{
    use HasFilterable;
    protected $fillable = ["locker_id", "amount"];

    public function model ()
    {
        return $this->morphTo();
    }
}
