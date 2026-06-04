@extends('layouts.app')

@section('content')
    <h1>Lead Finder</h1>

    <div class="card">
        <form method="GET" action="/lead-finder">
            <p>
                <label>Search Query</label><br>
                <input type="text" name="search_query" value="{{ $filters['search_query'] ?? '' }}" placeholder="Las Vegas dessert shops">
            </p>

            <p>
                <label>Lead Type</label><br>
                <select name="lead_type">
                    <option value="">All</option>
                    <option value="business" {{ ($filters['lead_type'] ?? '') === 'business' ? 'selected' : '' }}>Direct Business</option>
                    <option value="pr_agency" {{ ($filters['lead_type'] ?? '') === 'pr_agency' ? 'selected' : '' }}>PR Agency</option>
                </select>
            </p>

            <p>
                <label>Category</label><br>
                <input type="text" name="category" value="{{ $filters['category'] ?? '' }}" placeholder="Restaurant, PR Agency, Tourism PR">
            </p>

            <p>
                <label>City</label><br>
                <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="Las Vegas">
            </p>

            <button type="submit">Find Leads</button>
        </form>

        <p class="muted">
            Try: Las Vegas dessert shops, Las Vegas restaurants, Las Vegas family attractions,
            Las Vegas PR agencies, Las Vegas hospitality PR firms, Las Vegas influencer marketing agencies.
        </p>

        <p class="muted">
            Connect Google Places by setting <strong>GOOGLE_PLACES_API_KEY</strong> in your environment.
        </p>
    </div>

    @if($hasSearch)
        <h2>Results</h2>

        @forelse($results as $result)
            <div class="card">
                <h3>{{ $result['name'] }}</h3>
                <p><strong>Lead Type:</strong> {{ $result['lead_type'] === 'pr_agency' ? 'PR Agency' : 'Direct Business' }}</p>
                <p><strong>Category:</strong> {{ $result['category'] }}</p>
                <p><strong>Website:</strong> {{ $result['website'] ?: '-' }}</p>
                <p><strong>Phone:</strong> {{ $result['phone'] ?: '-' }}</p>
                <p><strong>Address:</strong> {{ $result['address'] ?: '-' }}</p>
                <p><strong>Google Rating:</strong> {{ is_null($result['rating']) ? '-' : number_format($result['rating'], 1) . '/5' }}</p>
                <p><strong>Instagram:</strong> {{ $result['instagram'] }}</p>
                <p><strong>Email:</strong> {{ $result['email'] }}</p>
                <p><strong>Fit Score:</strong> {{ $result['fit_score'] }}</p>
                <p><strong>Notes:</strong> {{ $result['notes'] }}</p>

                <form method="POST" action="/lead-finder/add">
                    @csrf
                    <input type="hidden" name="name" value="{{ $result['name'] }}">
                    <input type="hidden" name="lead_type" value="{{ $result['lead_type'] }}">
                    <input type="hidden" name="category" value="{{ $result['category'] }}">
                    <input type="hidden" name="website" value="{{ $result['website'] }}">
                    <input type="hidden" name="instagram" value="{{ $result['instagram'] }}">
                    <input type="hidden" name="email" value="{{ $result['email'] }}">
                    <input type="hidden" name="phone" value="{{ $result['phone'] }}">
                    <input type="hidden" name="address" value="{{ $result['address'] }}">
                    <input type="hidden" name="rating" value="{{ $result['rating'] }}">
                    <input type="hidden" name="place_id" value="{{ $result['place_id'] }}">
                    <input type="hidden" name="fit_score" value="{{ $result['fit_score'] }}">
                    <input type="hidden" name="notes" value="{{ $result['notes'] }}">
                    <input type="hidden" name="city" value="{{ $result['city'] }}">

                    @if(!empty($result['already_in_crm']))
                        <button type="submit" disabled>Already in CRM</button>
                    @else
                        <button type="submit">Add to CRM</button>
                    @endif
                </form>
            </div>
        @empty
            <p class="muted">No Google Places results matched. Try a broader query.</p>
        @endforelse
    @endif
@endsection
