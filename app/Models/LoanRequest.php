<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    protected $fillable = [
        'user_id',
        'tool_id',
        'status',
        'request_date',
        'approved_date',
        'due_date',
        'return_date',
        'admin_notes',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'approved_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }
}
