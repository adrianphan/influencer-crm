@extends('layouts.app')

@section('content')
    <style>
        .profile-hero {
            border-radius: 14px;
            padding: 16px;
            color: #fff;
            background: linear-gradient(135deg, #1f6feb 0%, #0f7b6c 65%, #1946c7 100%);
            margin-bottom: 16px;
        }

        .profile-hero h1 {
            margin-bottom: 6px;
            color: #fff;
        }

        .platform-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .platform-card {
            border-radius: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .platform-card h3 {
            margin: 0 0 4px;
            color: #fff;
        }

        .platform-card p {
            margin: 0;
            font-size: 14px;
            opacity: 0.95;
        }
    </style>

    <div class="profile-hero">
        <h1>Creator Profile</h1>
        <p>Keep your media identity up to date so every outreach message reflects your true creator brand.</p>

        <div class="platform-grid">
            <div class="platform-card">
                <h3>Instagram</h3>
                <p>{{ $profile->instagram_handle ?? '@yourhandle' }}</p>
                <p>{{ number_format((int) ($profile->instagram_followers ?? 0)) }} followers</p>
            </div>
            <div class="platform-card">
                <h3>TikTok</h3>
                <p>{{ $profile->tiktok_handle ?? '@yourhandle' }}</p>
                <p>{{ number_format((int) ($profile->tiktok_followers ?? 0)) }} followers</p>
            </div>
            <div class="platform-card">
                <h3>YouTube</h3>
                <p>{{ $profile->youtube_handle ?? '@yourchannel' }}</p>
                <p>{{ number_format((int) ($profile->youtube_subscribers ?? 0)) }} subscribers</p>
            </div>
        </div>
    </div>

    @if($profile)
        <h2>Edit Profile</h2>
        <form method="POST" action="/creator-profile">
            @csrf
            @method('PUT')
    @else
        <h2>Create Profile</h2>
        <form method="POST" action="/creator-profile">
            @csrf
    @endif

        <p>
            <label>Name</label><br>
            <input type="text" name="name" value="{{ old('name', $profile->name ?? '') }}">
        </p>

        <p>
            <label>Display Name</label><br>
            <input type="text" name="display_name" value="{{ old('display_name', $profile->display_name ?? '') }}">
        </p>

        <p>
            <label>Instagram Handle</label><br>
            <input type="text" name="instagram_handle" value="{{ old('instagram_handle', $profile->instagram_handle ?? '') }}">
        </p>

        <p>
            <label>Instagram Followers</label><br>
            <input type="number" name="instagram_followers" value="{{ old('instagram_followers', $profile->instagram_followers ?? '') }}">
        </p>

        <p>
            <label>TikTok Handle</label><br>
            <input type="text" name="tiktok_handle" value="{{ old('tiktok_handle', $profile->tiktok_handle ?? '') }}">
        </p>

        <p>
            <label>TikTok Followers</label><br>
            <input type="number" name="tiktok_followers" value="{{ old('tiktok_followers', $profile->tiktok_followers ?? '') }}">
        </p>

        <p>
            <label>YouTube Handle</label><br>
            <input type="text" name="youtube_handle" value="{{ old('youtube_handle', $profile->youtube_handle ?? '') }}">
        </p>

        <p>
            <label>YouTube Subscribers</label><br>
            <input type="number" name="youtube_subscribers" value="{{ old('youtube_subscribers', $profile->youtube_subscribers ?? '') }}">
        </p>

        <p>
            <label>Location</label><br>
            <input type="text" name="location" value="{{ old('location', $profile->location ?? '') }}">
        </p>

        <p>
            <label>Niche</label><br>
            <input type="text" name="niche" value="{{ old('niche', $profile->niche ?? '') }}">
        </p>

        <p>
            <label>Bio</label><br>
            <textarea name="bio" rows="4">{{ old('bio', $profile->bio ?? '') }}</textarea>
        </p>

        <p>
            <label>Media Kit URL</label><br>
            <input type="text" name="media_kit_url" value="{{ old('media_kit_url', $profile->media_kit_url ?? '') }}">
        </p>

        <p>
            <label>Email Signature</label><br>
            <textarea name="email_signature" rows="4">{{ old('email_signature', $profile->email_signature ?? '') }}</textarea>
        </p>

        <p>
            <label>Audience Notes</label><br>
            <textarea name="audience_notes" rows="4">{{ old('audience_notes', $profile->audience_notes ?? '') }}</textarea>
        </p>

        <button type="submit">Save Profile</button>
    </form>

    <p><a href="/businesses">Back to Businesses</a></p>
@endsection
