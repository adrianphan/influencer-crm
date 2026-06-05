@extends('layouts.app')

@section('content')
    <style>
        .hero {
            padding: 18px;
            border-radius: 16px;
            color: #ffffff;
            background: linear-gradient(135deg, #0f7b6c 0%, #0a5da8 60%, #1946c7 100%);
            margin-bottom: 16px;
            box-shadow: 0 14px 30px rgba(15, 123, 108, 0.25);
        }

        .hero h1 {
            color: #fff;
            margin-bottom: 6px;
        }

        .hero p {
            margin: 0;
            opacity: 0.92;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .stat-chip {
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 10px;
        }

        .stat-chip strong {
            font-size: 18px;
            display: block;
        }

        .platform-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .platform-card {
            border-radius: 14px;
            padding: 14px;
            color: #fff;
        }

        .platform-card h3 {
            margin-bottom: 4px;
            color: inherit;
        }

        .platform-card p {
            margin: 0;
            opacity: 0.95;
        }

        .platform-link {
            color: #ffffff;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .platform-link:hover {
            filter: brightness(0.95);
        }

        .platform-instagram {
            background: linear-gradient(135deg, #833ab4 0%, #fd1d1d 55%, #fcb045 100%);
        }

        .platform-tiktok {
            background: linear-gradient(135deg, #111827 0%, #1f2937 55%, #0f7b6c 100%);
        }

        .platform-youtube {
            background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%);
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 12px;
        }

        .chart-row {
            margin-bottom: 8px;
        }

        .chart-label {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .chart-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #ececec;
            overflow: hidden;
        }

        .chart-fill {
            height: 8px;
            border-radius: 999px;
            background: #1f6feb;
        }
    </style>

    <div class="hero">
        <h1>
            {{ $creatorProfile->display_name ?? 'Creator' }}'s Influencer CRM
        </h1>
        <p>
            Audience Snapshot: {{ number_format($totalAudience) }} total followers across TikTok, Instagram, and YouTube.
        </p>

        <div class="stats-grid">
            <div class="stat-chip">
                <span>Total Leads</span>
                <strong>{{ $totalLeads }}</strong>
            </div>
            <div class="stat-chip">
                <span>Outreach Sent</span>
                <strong>{{ $outreachSentCount }}</strong>
            </div>
            <div class="stat-chip">
                <span>Replies</span>
                <strong>{{ $repliesCount }}</strong>
            </div>
            <div class="stat-chip">
                <span>Booked</span>
                <strong>{{ $bookedCount }}</strong>
            </div>
            <div class="stat-chip">
                <span>Completed</span>
                <strong>{{ $completedCount }}</strong>
            </div>
        </div>
    </div>

    @php
        $instagramHandle = trim((string) ($creatorProfile->instagram_handle ?? ''));
        $tiktokHandle = trim((string) ($creatorProfile->tiktok_handle ?? ''));
        $youtubeHandle = trim((string) ($creatorProfile->youtube_handle ?? ''));

        $instagramUrl = '';
        if ($instagramHandle !== '') {
            $instagramUrl = \Illuminate\Support\Str::startsWith($instagramHandle, ['http://', 'https://'])
                ? $instagramHandle
                : 'https://www.instagram.com/' . ltrim($instagramHandle, '@/');
        }

        $tiktokUrl = '';
        if ($tiktokHandle !== '') {
            $tiktokUrl = \Illuminate\Support\Str::startsWith($tiktokHandle, ['http://', 'https://'])
                ? $tiktokHandle
                : 'https://www.tiktok.com/@' . ltrim($tiktokHandle, '@/');
        }

        $youtubeUrl = '';
        if ($youtubeHandle !== '') {
            if (\Illuminate\Support\Str::startsWith($youtubeHandle, ['http://', 'https://'])) {
                $youtubeUrl = $youtubeHandle;
            } elseif (\Illuminate\Support\Str::startsWith($youtubeHandle, 'UC')) {
                $youtubeUrl = 'https://www.youtube.com/channel/' . $youtubeHandle;
            } else {
                $youtubeUrl = 'https://www.youtube.com/@' . ltrim($youtubeHandle, '@/');
            }
        }
    @endphp

    <div class="platform-grid">
        <div class="platform-card platform-instagram">
            <h3>Instagram</h3>
            <p>
                @if($instagramUrl)
                    <a class="platform-link" href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer">{{ $creatorProfile->instagram_handle }}</a>
                @else
                    @yourhandle
                @endif
            </p>
            <p><strong>{{ number_format($instagramFollowers) }}</strong> followers</p>
        </div>
        <div class="platform-card platform-tiktok">
            <h3>TikTok</h3>
            <p>
                @if($tiktokUrl)
                    <a class="platform-link" href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer">{{ $creatorProfile->tiktok_handle }}</a>
                @else
                    @yourhandle
                @endif
            </p>
            <p><strong>{{ number_format($tiktokFollowers) }}</strong> followers</p>
        </div>
        <div class="platform-card platform-youtube">
            <h3>YouTube</h3>
            <p>
                @if($youtubeUrl)
                    <a class="platform-link" href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer">{{ $creatorProfile->youtube_handle }}</a>
                @else
                    @yourchannel
                @endif
            </p>
            <p><strong>{{ number_format($youtubeSubscribers) }}</strong> subscribers</p>
        </div>
    </div>

    <div class="card">
        <h2>Dashboard Stats</h2>
        <ul>
            <li>Total Leads: {{ $totalLeads }}</li>
            <li>Outreach Sent: {{ $outreachSentCount }}</li>
            <li>Replies: {{ $repliesCount }}</li>
            <li>Interested: {{ $interestedCount }}</li>
            <li>Booked: {{ $bookedCount }}</li>
            <li>Content Created: {{ $contentCreatedCount }}</li>
            <li>Posted: {{ $postedCount }}</li>
            <li>Paid: {{ $paidCount }}</li>
            <li>Completed: {{ $completedCount }}</li>
            <li>Follow-Ups Due: {{ $followUpsDueCount }}</li>
        </ul>
    </div>

    <div class="card">
        <h2>Reporting Charts</h2>

        <div class="chart-grid">
            <div>
                <h3>Leads by Month</h3>
                @php $maxLeads = max(1, collect($leadsByMonth)->max('value')); @endphp
                @foreach($leadsByMonth as $point)
                    <div class="chart-row">
                        <div class="chart-label">
                            <span>{{ $point['label'] }}</span>
                            <strong>{{ $point['value'] }}</strong>
                        </div>
                        <div class="chart-track">
                            <div class="chart-fill" style="width: {{ round(($point['value'] / $maxLeads) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <h3>Bookings by Month</h3>
                @php $maxBookings = max(1, collect($bookingsByMonth)->max('value')); @endphp
                @foreach($bookingsByMonth as $point)
                    <div class="chart-row">
                        <div class="chart-label">
                            <span>{{ $point['label'] }}</span>
                            <strong>{{ $point['value'] }}</strong>
                        </div>
                        <div class="chart-track">
                            <div class="chart-fill" style="width: {{ round(($point['value'] / $maxBookings) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <h3>Top Categories</h3>
                @php $maxCategories = max(1, collect($topCategories)->max('value')); @endphp
                @forelse($topCategories as $point)
                    <div class="chart-row">
                        <div class="chart-label">
                            <span>{{ $point['label'] }}</span>
                            <strong>{{ $point['value'] }}</strong>
                        </div>
                        <div class="chart-track">
                            <div class="chart-fill" style="width: {{ round(($point['value'] / $maxCategories) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="muted">No category data yet.</p>
                @endforelse
            </div>

            <div>
                <h3>Top PR Agencies</h3>
                @php $maxAgencies = max(1, collect($topPrAgencies)->max('value')); @endphp
                @forelse($topPrAgencies as $point)
                    <div class="chart-row">
                        <div class="chart-label">
                            <span>{{ $point['label'] }}</span>
                            <strong>{{ $point['value'] }}</strong>
                        </div>
                        <div class="chart-track">
                            <div class="chart-fill" style="width: {{ round(($point['value'] / $maxAgencies) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="muted">No PR agency data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Filters</h2>

        <form method="GET" action="/businesses">
            <p>
                <label>Status</label><br>
                <select name="status">
                    <option value="">All</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $statusFilter === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </p>

            <p>
                <label>Lead Type</label><br>
                <select name="lead_type">
                    <option value="">All</option>
                    <option value="business" {{ $leadTypeFilter === 'business' ? 'selected' : '' }}>Direct Business</option>
                    <option value="pr_agency" {{ $leadTypeFilter === 'pr_agency' ? 'selected' : '' }}>PR Agency</option>
                </select>
            </p>

            <p>
                <label>Category</label><br>
                <select name="category">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ $categoryFilter === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </p>

            <p>
                <label>
                    <input type="checkbox" name="follow_ups_due_only" value="1" {{ $followUpsDueOnly ? 'checked' : '' }}>
                    Follow-ups due only
                </label>
            </p>

            <button type="submit">Apply Filters</button>
            <a href="/businesses">Clear</a>
        </form>
    </div>

    <div class="card">
        <h2>Follow Ups Due</h2>

        @if($followUpsDue->count())
            @foreach($followUpsDue as $business)
                <div style="border:1px solid red; padding:12px; margin-bottom:10px;">
                    <h3><a href="/businesses/{{ $business->id }}">{{ $business->name }}</a></h3>
                    <p>Status: {{ $business->status }}</p>
                    <p>Follow Up: {{ $business->follow_up_at }}</p>
                </div>
            @endforeach
        @else
            <p class="muted">No follow-ups due.</p>
        @endif
    </div>

    <h2>Direct Business Pipeline</h2>

    @if($totalLeads === 0)
        <div class="card">
            <p class="muted">No businesses yet. Start by adding your first lead.</p>
        </div>
    @endif

    @foreach($businessStatuses as $status)
        <div class="card">
            <h3>{{ $status }}</h3>

            @forelse($directBusinessesByStatus->get($status, collect()) as $business)
                <div style="padding:10px; margin-bottom:10px; background:#f7f7f7; border-radius:6px;">
                    <h4><a href="/businesses/{{ $business->id }}">{{ $business->name }}</a></h4>
                    <p>{{ $business->category }}</p>
                    <p>{{ $business->instagram }}</p>
                    <p>{{ $business->email }}</p>

                    @if($business->follow_up_at)
                        <p>Follow Up: {{ $business->follow_up_at }}</p>
                    @endif
                </div>
            @empty
                <p class="muted">No businesses in this stage.</p>
            @endforelse
        </div>
    @endforeach

    <h2>PR Agency Pipeline</h2>

    @foreach($prAgencyStatuses as $status)
        <div class="card">
            <h3>{{ $status }}</h3>

            @forelse($prAgencyBusinessesByStatus->get($status, collect()) as $business)
                <div style="padding:10px; margin-bottom:10px; background:#f7f7f7; border-radius:6px;">
                    <h4><a href="/businesses/{{ $business->id }}">{{ $business->name }}</a></h4>
                    <p>{{ $business->category }}</p>
                    <p>{{ $business->instagram }}</p>
                    <p>{{ $business->email }}</p>

                    @if($business->follow_up_at)
                        <p>Follow Up: {{ $business->follow_up_at }}</p>
                    @endif
                </div>
            @empty
                <p class="muted">No PR agency leads in this stage.</p>
            @endforelse
        </div>
    @endforeach
@endsection
