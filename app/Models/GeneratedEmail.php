<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Business;

class GeneratedEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'type',
        'subject',
        'body',
        'draft_id',
        'draft_created_at',
    ];

    protected $casts = [
        'draft_created_at' => 'datetime',
    ];

public function business()
{
    return $this->belongsTo(Business::class);
}
}
