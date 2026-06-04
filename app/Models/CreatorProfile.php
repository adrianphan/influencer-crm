<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreatorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'instagram_handle',
        'instagram_followers',
        'tiktok_handle',
        'tiktok_followers',
        'youtube_handle',
        'youtube_subscribers',
        'location',
        'niche',
        'bio',
        'media_kit_url',
        'email_signature',
        'audience_notes',
    ];
}
