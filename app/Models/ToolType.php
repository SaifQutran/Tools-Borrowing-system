<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolType extends Model
{
    protected $fillable = ['name'];

    public function tools()
    {
        return $this->hasMany(Tool::class);
    }
}
