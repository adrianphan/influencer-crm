<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneratedEmail;
use App\Models\Interaction;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lead_type',
        'category',
        'website',
        'instagram',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'contact_source',
        'agency_specialties',
        'pr_contact_role',
        'client_types',
        'roster_status',
        'media_kit_sent_at',
        'collab_value',
        'deliverables',
        'compensation',
        'booking_date',
        'posting_date',
        'posted_url',
        'payment_status',
        'contact_name',
        'status',
        'fit_score',
        'scoring_notes',
        'notes',
        'last_contacted_at',
        'follow_up_at',
    ];

    public function generatedEmails()
    {
        return $this->hasMany(GeneratedEmail::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }
}
