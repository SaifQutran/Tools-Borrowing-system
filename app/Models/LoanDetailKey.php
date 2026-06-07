<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDetailKey extends Model
{
    protected $fillable = [
        'name',
        'value_type',
    ];
}
