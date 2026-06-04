@extends('layouts.app')

@section('content')
    <style>
        .action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 16px 0;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid #333;
            border-radius: 6px;
            text-decoration: none;
            color: #111;
            background: #f6f6f6;
            font-size: 14px;
        }

        .btn:hover {
            background: #ececec;
        }

        .btn-primary {
            background: #1f6feb;
            border-color: #1f6feb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #165ec7;
        }

        .email-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .email-meta {
            color: #555;
            margin: 8px 0;
        }
    </style>

    <h1>{{ $business->name }}</h1>
    @php
        $businessStatuses = [
            'Lead Found',
            'Outreach Sent',
            'Interested',
            'Booked',
            'Completed',
            'Rejected',
        ];

        $prAgencyStatuses = [
            'Lead Found',
            'Outreach Sent',
            'Intro Call',
            'Added to Influencer List',
            'Receiving Opportunities',
            'Inactive',
            'Rejected',
        ];

        $availableStatuses = $business->lead_type === 'pr_agency' ? $prAgencyStatuses : $businessStatuses;
    @endphp

    <p>
        <strong>Lead Type:</strong>
        {{ $business->lead_type === 'pr_agency' ? 'PR Agency' : 'Direct Business' }}
    </p>

    <p><strong>Category:</strong> {{ $business->category }}</p>
    <p>
        <strong>Website:</strong>
        @if($business->website)
            <a href="{{ $business->website }}" target="_blank" rel="noopener noreferrer">{{ $business->website }}</a>
        @else
            -
        @endif
    </p>
    <p>
        <strong>Instagram:</strong>
        @if($business->instagram)
            <a href="{{ $business->instagram }}" target="_blank" rel="noopener noreferrer">{{ $business->instagram }}</a>
        @else
            -
        @endif
    </p>
    <p>
        <strong>Email:</strong>
        @if($business->email)
            <a href="mailto:{{ $business->email }}">{{ $business->email }}</a>
        @else
            -
        @endif
    </p>
    <p><strong>Phone:</strong> {{ $business->phone ?: '-' }}</p>
    <p><strong>Address:</strong> {{ $business->address ?: '-' }}</p>
    <p><strong>City:</strong> {{ $business->city ?: '-' }}</p>
    <p><strong>State:</strong> {{ $business->state ?: '-' }}</p>
    <p><strong>Contact Source:</strong> {{ $business->contact_source ?: '-' }}</p>
    <p><strong>Collab Value:</strong> {{ $business->collab_value ?: '-' }}</p>
    <p><strong>Deliverables:</strong> {{ $business->deliverables ?: '-' }}</p>
    <p><strong>Compensation:</strong> {{ $business->compensation ?: '-' }}</p>
    <p><strong>Booking Date:</strong> {{ $business->booking_date ?: '-' }}</p>
    <p><strong>Posting Date:</strong> {{ $business->posting_date ?: '-' }}</p>
    <p>
        <strong>Posted URL:</strong>
        @if($business->posted_url)
            <a href="{{ $business->posted_url }}" target="_blank" rel="noopener noreferrer">{{ $business->posted_url }}</a>
        @else
            -
        @endif
    </p>
    <p><strong>Payment Status:</strong> {{ $business->payment_status ?: '-' }}</p>
    <p><strong>Contact:</strong> {{ $business->contact_name }}</p>
    <p><strong>Contact Role:</strong> {{ $business->pr_contact_role ?: '-' }}</p>
    <p><strong>Agency Specialties:</strong> {{ $business->agency_specialties ?: '-' }}</p>
    <p><strong>Client Types:</strong> {{ $business->client_types ?: '-' }}</p>
    <p><strong>Roster Status:</strong> {{ $business->roster_status ?: '-' }}</p>
    <p><strong>Media Kit Sent Date:</strong> {{ $business->media_kit_sent_at ?: '-' }}</p>
    <p><strong>Fit Score:</strong> {{ $business->fit_score ?: '-' }}</p>
    <p><strong>Scoring Notes:</strong><br>{!! nl2br(e($business->scoring_notes ?: '-')) !!}</p>
    <p><strong>Status:</strong> {{ $business->status }}</p>
    <p><strong>Notes:</strong> {{ $business->notes }}</p>

    <hr>

    <h2>Update CRM Info</h2>

    <form method="POST" action="/businesses/{{ $business->id }}">
        @csrf
        @method('PUT')

        <p>
            <label>Business or Agency Name</label><br>
            <input type="text" name="name" value="{{ $business->name }}">
        </p>

        <p>
            <label>Lead Type</label><br>
            <select name="lead_type">
                <option value="business" {{ $business->lead_type === 'business' ? 'selected' : '' }}>Direct Business</option>
                <option value="pr_agency" {{ $business->lead_type === 'pr_agency' ? 'selected' : '' }}>PR Agency</option>
            </select>
        </p>

        <p>
            <label>Status</label><br>
            <select name="status">
                @foreach($availableStatuses as $statusOption)
                    <option value="{{ $statusOption }}" {{ $business->status == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                @endforeach
            </select>
        </p>

        <p>
            <label>Contact Name</label><br>
            <input type="text" name="contact_name" value="{{ $business->contact_name }}">
        </p>

        <p>
            <label>Contact Role</label><br>
            <input type="text" name="pr_contact_role" value="{{ $business->pr_contact_role }}">
        </p>

        <p>
            <label>Email</label><br>
            <input type="text" name="email" value="{{ $business->email }}">
        </p>

        <p>
            <label>Website</label><br>
            <input type="text" name="website" value="{{ $business->website }}">
        </p>

        <p>
            <label>Instagram</label><br>
            <input type="text" name="instagram" value="{{ $business->instagram }}">
        </p>

        <p>
            <label>Agency Specialties</label><br>
            <textarea name="agency_specialties" rows="3">{{ $business->agency_specialties }}</textarea>
        </p>

        <p>
            <label>Client Types</label><br>
            <textarea name="client_types" rows="3">{{ $business->client_types }}</textarea>
        </p>

        <p>
            <label>Roster Status</label><br>
            <input type="text" name="roster_status" value="{{ $business->roster_status }}">
        </p>

        <p>
            <label>Media Kit Sent Date</label><br>
            <input type="date" name="media_kit_sent_at" value="{{ $business->media_kit_sent_at }}">
        </p>

        <p>
            <label>Last Contacted</label><br>
            <input type="date" name="last_contacted_at" value="{{ $business->last_contacted_at }}">
        </p>

        <p>
            <label>Phone</label><br>
            <input type="text" name="phone" value="{{ $business->phone }}">
        </p>

        <p>
            <label>Address</label><br>
            <input type="text" name="address" value="{{ $business->address }}">
        </p>

        <p>
            <label>City</label><br>
            <input type="text" name="city" value="{{ $business->city }}">
        </p>

        <p>
            <label>State</label><br>
            <input type="text" name="state" value="{{ $business->state }}">
        </p>

        <p>
            <label>Contact Source</label><br>
            <input type="text" name="contact_source" value="{{ $business->contact_source }}">
        </p>

        <p>
            <label>Collab Value</label><br>
            <input type="number" step="0.01" name="collab_value" value="{{ $business->collab_value }}">
        </p>

        <p>
            <label>Deliverables</label><br>
            <textarea name="deliverables" rows="4">{{ $business->deliverables }}</textarea>
        </p>

        <p>
            <label>Compensation</label><br>
            <input type="number" step="0.01" name="compensation" value="{{ $business->compensation }}">
        </p>

        <p>
            <label>Booking Date</label><br>
            <input type="date" name="booking_date" value="{{ $business->booking_date }}">
        </p>

        <p>
            <label>Posting Date</label><br>
            <input type="date" name="posting_date" value="{{ $business->posting_date }}">
        </p>

        <p>
            <label>Posted URL</label><br>
            <input type="text" name="posted_url" value="{{ $business->posted_url }}">
        </p>

        <p>
            <label>Payment Status</label><br>
            <select name="payment_status">
                <option value="" {{ empty($business->payment_status) ? 'selected' : '' }}>Select Payment Status</option>
                <option value="Pending" {{ $business->payment_status === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Partially Paid" {{ $business->payment_status === 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="Paid" {{ $business->payment_status === 'Paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </p>

        <p>
            <label>Follow Up Date</label><br>
            <input type="date" name="follow_up_at" value="{{ $business->follow_up_at }}">
        </p>

        <p>
            <label>Notes</label><br>
            <textarea name="notes" rows="5">{{ $business->notes }}</textarea>
        </p>

        <button type="submit">Update Business</button>
    </form>

    <hr>

    <h2>Generated Emails</h2>

    @forelse($business->generatedEmails as $email)
        @php
            $typeLabels = [
                'outreach' => 'Outreach Email',
                'instagram_dm' => 'Instagram DM',
                'follow_up' => 'Follow-Up',
                'pr_agency_outreach' => 'PR Agency Outreach',
                'pr_agency_follow_up' => 'PR Agency Follow-Up',
            ];
        @endphp

        <div class="email-card">
            <strong><a href="/generated-emails/{{ $email->id }}">{{ $email->subject }}</a></strong>
            <p class="email-meta">
                Type: {{ $typeLabels[$email->type] ?? str_replace('_', ' ', ucfirst($email->type)) }} |
                Created: {{ $email->created_at->format('M d, Y g:i A') }}
            </p>
            <p>{{ \Illuminate\Support\Str::limit($email->body, 150) }}</p>
            <a class="btn" href="/generated-emails/{{ $email->id }}">Open full message</a>
        </div>
    @empty
        <p>No generated emails yet.</p>
    @endforelse

    <div class="action-row">
        <a class="btn btn-primary" href="/businesses/{{ $business->id }}/generate-email?log_interaction=1">Generate Outreach Email</a>
        <a class="btn btn-primary" href="/businesses/{{ $business->id }}/generate-dm?log_interaction=1">Generate Instagram DM</a>
        <a class="btn btn-primary" href="/businesses/{{ $business->id }}/generate-follow-up?log_interaction=1">Generate Follow-Up</a>
        @if($business->lead_type === 'pr_agency')
            <a class="btn btn-primary" href="/businesses/{{ $business->id }}/generate-pr-outreach?log_interaction=1">Generate PR Agency Outreach</a>
            <a class="btn btn-primary" href="/businesses/{{ $business->id }}/generate-pr-follow-up?log_interaction=1">Generate PR Agency Follow-Up</a>
        @endif
        <a class="btn" href="/businesses">Back to Businesses</a>
    </div>

    <p class="muted">
        Safety: Generated messages are drafts only. Review and manually send them outside this CRM.
    </p>

    <hr>

    <h2>Interaction Timeline</h2>

    @forelse($business->interactions as $interaction)
        <div class="email-card">
            <p class="email-meta">Type: {{ strtoupper($interaction->type) }} | Direction: {{ ucfirst($interaction->direction) }} | Occurred: {{ \Illuminate\Support\Carbon::parse($interaction->occurred_at)->format('M d, Y g:i A') }}</p>
            <p>{{ $interaction->content }}</p>
        </div>
    @empty
        <p>No interactions yet.</p>
    @endforelse

    <h3>Add Manual Interaction</h3>

    <form method="POST" action="/businesses/{{ $business->id }}/interactions">
        @csrf

        <p>
            <label>Type</label><br>
            <select name="type">
                <option value="email">Email</option>
                <option value="dm">DM</option>
                <option value="phone">Phone</option>
                <option value="meeting">Meeting</option>
                <option value="note">Note</option>
            </select>
        </p>

        <p>
            <label>Direction</label><br>
            <select name="direction">
                <option value="outbound">Outbound</option>
                <option value="inbound">Inbound</option>
            </select>
        </p>

        <p>
            <label>Occurred At</label><br>
            <input type="datetime-local" name="occurred_at" value="{{ now()->format('Y-m-d\\TH:i') }}">
        </p>

        <p>
            <label>Content</label><br>
            <textarea name="content" rows="5"></textarea>
        </p>

        <button type="submit">Add Interaction</button>
    </form>
@endsection
