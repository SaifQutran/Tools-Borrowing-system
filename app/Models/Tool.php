<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'name',
        'tool_type_id',
        'code',
        'status',
        'attributes',
        'qr_code_path',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    // Relationships
    public function toolType()
    {
        return $this->belongsTo(ToolType::class);
    }

    public function loanRequests()
    {
        return $this->hasMany(LoanRequest::class);
    }

    public function currentLoan()
    {
        return $this->hasOne(LoanRequest::class)
            ->where('status', 'approved')
            ->latest();
    }
}
