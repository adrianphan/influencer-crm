<?php

namespace App\Http\Controllers;

use App\Models\CreatorProfile;
use Illuminate\Http\Request;

class CreatorProfileController extends Controller
{
    public function show()
    {
        $profile = CreatorProfile::query()->first();

        return view('creator-profile.show', compact('profile'));
    }

    public function store(Request $request)
    {
        if (CreatorProfile::query()->exists()) {
            return redirect('/creator-profile');
        }

        CreatorProfile::create($this->validateProfile($request));

        return redirect('/creator-profile')->with('success', 'Creator profile created.');
    }

    public function update(Request $request)
    {
        $profile = CreatorProfile::query()->first();

        if (!$profile) {
            CreatorProfile::create($this->validateProfile($request));

            return redirect('/creator-profile')->with('success', 'Creator profile created.');
        }

        $profile->update($this->validateProfile($request));

        return redirect('/creator-profile')->with('success', 'Creator profile updated.');
    }

    private function validateProfile(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'instagram_handle' => 'nullable|string|max:255',
            'instagram_followers' => 'nullable|integer|min:0',
            'tiktok_handle' => 'nullable|string|max:255',
            'tiktok_followers' => 'nullable|integer|min:0',
            'youtube_handle' => 'nullable|string|max:255',
            'youtube_subscribers' => 'nullable|integer|min:0',
            'location' => 'required|string|max:255',
            'niche' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'media_kit_url' => 'nullable|url|max:255',
            'email_signature' => 'nullable|string',
            'audience_notes' => 'nullable|string',
        ]);
    }
}
