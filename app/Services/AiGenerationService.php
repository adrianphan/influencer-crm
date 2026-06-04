<?php

namespace App\Services;

use App\Models\Business;
use App\Models\CreatorProfile;

class AiGenerationService
{
    public function generateOutreachEmail(Business $business, CreatorProfile $profile)
    {
        $fallbackSubject = 'Collaboration Opportunity with ' . $profile->display_name;

        $audienceSummary = $this->buildAudienceSummary($profile);
        $signature = $profile->email_signature ?: "Best,\n" . $profile->display_name;
        $bioSection = $profile->bio ? "\n{$profile->bio}\n" : "\n";

        $fallbackBody = "Hi {$business->name},

My name is {$profile->name}, and I'm a {$profile->location} {$profile->niche} creator with {$audienceSummary}.

{$bioSection}
I came across {$business->name} and thought it would be a great fit for my audience.

I'd love to explore a collaboration where I can visit, create content, and share the experience with my community.

Would you be open to discussing a potential partnership?

{$signature}";

        $prompt = $this->buildPrompt(
            'Write a short, warm outreach email for a potential brand collaboration. Return JSON with keys: subject and body.',
            $business,
            $profile
        );

        return $this->generateWithFallback($prompt, $fallbackSubject, $fallbackBody);
    }

    public function generateInstagramDm(Business $business, CreatorProfile $profile)
    {
        $fallbackSubject = 'Instagram DM';
        $instagramHandle = $profile->instagram_handle ?: '@mrsmariealvarezp';

        $fallbackBody = "Hi {$business->name}! {$profile->display_name} here ({$instagramHandle}).

I'm a {$profile->niche} creator and came across your page. I'd love to explore a collaboration and create content highlighting {$business->name} for my audience.

Would you be open to chatting about a potential partnership?";

        $prompt = $this->buildPrompt(
            'Write an Instagram DM for first outreach. Keep it concise and natural. Return JSON with keys: subject and body. Subject should be Instagram DM.',
            $business,
            $profile
        );

        return $this->generateWithFallback($prompt, $fallbackSubject, $fallbackBody);
    }

    public function generateFollowUp(Business $business, CreatorProfile $profile)
    {
        $fallbackSubject = 'Following Up on Collaboration Opportunity';
        $signature = $profile->email_signature ?: "Best,\n" . $profile->display_name;

        $fallbackBody = "Hi {$business->name},

I just wanted to follow up on my previous message about a potential collaboration.

I'd still love to connect and explore a way to highlight {$business->name} for my {$profile->location} {$profile->niche} audience.

Would you be open to chatting this week?

{$signature}";

        $prompt = $this->buildPrompt(
            'Write a friendly follow-up email for a brand collaboration pitch. Return JSON with keys: subject and body.',
            $business,
            $profile
        );

        return $this->generateWithFallback($prompt, $fallbackSubject, $fallbackBody);
    }

    public function generatePrOutreach(Business $business, CreatorProfile $profile)
    {
        $fallbackSubject = 'Creator Introduction: ' . $profile->display_name . ' for Future Campaigns';
        $signature = $profile->email_signature ?: "Best,\n" . $profile->display_name;
        $audienceSummary = $this->buildAudienceSummary($profile);

        $fallbackBody = "Hi {$business->contact_name},

I hope you're doing well. My name is {$profile->name}, and I'm a Las Vegas {$profile->niche} creator.

I create warm, high-quality content around food, lifestyle, family, restaurants, hospitality, events, and travel. My audience is {$audienceSummary}, and I love helping brands tell authentic local stories.

I'd love to introduce myself and be considered for your creator/influencer roster for future campaigns.

If helpful, I'm happy to share my media kit and examples of recent collaboration work.

{$signature}";

        $prompt = $this->buildPrompt(
            'Write a warm, professional, confident PR agency outreach email from a Las Vegas creator asking to be added to their influencer list for future campaigns. Return JSON with keys: subject and body.',
            $business,
            $profile
        );

        return $this->generateWithFallback($prompt, $fallbackSubject, $fallbackBody);
    }

    public function generatePrFollowUp(Business $business, CreatorProfile $profile)
    {
        $fallbackSubject = 'Following Up: Creator Roster Opportunity';
        $signature = $profile->email_signature ?: "Best,\n" . $profile->display_name;

        $fallbackBody = "Hi {$business->contact_name},

I wanted to follow up on my previous note and re-introduce myself as a Las Vegas {$profile->niche} creator.

I'd still love to be considered for your creator roster and future campaigns involving food, lifestyle, family, restaurants, hospitality, events, or travel.

If useful, I can send over my media kit and availability.

{$signature}";

        $prompt = $this->buildPrompt(
            'Write a concise, warm, professional follow-up email to a PR agency about being added to their influencer roster. Return JSON with keys: subject and body.',
            $business,
            $profile
        );

        return $this->generateWithFallback($prompt, $fallbackSubject, $fallbackBody);
    }

    // Backward-compatible wrappers for existing controller calls.
    public function generatePrAgencyOutreach(Business $business, CreatorProfile $profile)
    {
        return $this->generatePrOutreach($business, $profile);
    }

    public function generatePrAgencyFollowUp(Business $business, CreatorProfile $profile)
    {
        return $this->generatePrFollowUp($business, $profile);
    }

    private function generateWithFallback($prompt, $fallbackSubject, $fallbackBody)
    {
        $apiKey = config('services.openai.api_key');
        $model = env('OPENAI_MODEL', 'gpt-4o-mini');

        if (!$apiKey) {
            return [
                'subject' => $fallbackSubject,
                'body' => $fallbackBody,
            ];
        }

        try {
            $client = \OpenAI::client($apiKey);

            $response = $client->chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an assistant that writes outreach copy for creators. Always return valid JSON with subject and body.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
            ]);

            $content = $response->choices[0]->message->content ?? '';
            $parsed = json_decode($content, true);

            if (!is_array($parsed) || empty($parsed['body'])) {
                return [
                    'subject' => $fallbackSubject,
                    'body' => $fallbackBody,
                ];
            }

            return [
                'subject' => $parsed['subject'] ?? $fallbackSubject,
                'body' => $parsed['body'],
            ];
        } catch (\Throwable $e) {
            return [
                'subject' => $fallbackSubject,
                'body' => $fallbackBody,
            ];
        }
    }

    private function buildPrompt($instruction, Business $business, CreatorProfile $profile)
    {
        $previousMessages = $business->generatedEmails()
            ->latest()
            ->take(3)
            ->get(['type', 'subject', 'body'])
            ->map(function ($item) {
                return [
                    'type' => $item->type,
                    'subject' => $item->subject,
                    'body' => $item->body,
                ];
            })
            ->toArray();

        $creatorData = [
            'name' => $profile->name,
            'display_name' => $profile->display_name,
            'instagram_handle' => $profile->instagram_handle,
            'instagram_followers' => $profile->instagram_followers,
            'tiktok_handle' => $profile->tiktok_handle,
            'tiktok_followers' => $profile->tiktok_followers,
            'youtube_handle' => $profile->youtube_handle,
            'youtube_subscribers' => $profile->youtube_subscribers,
            'location' => $profile->location,
            'niche' => $profile->niche,
            'bio' => $profile->bio,
            'media_kit_url' => $profile->media_kit_url,
            'email_signature' => $profile->email_signature,
            'audience_notes' => $profile->audience_notes,
        ];

        $businessData = [
            'name' => $business->name,
            'lead_type' => $business->lead_type,
            'category' => $business->category,
            'website' => $business->website,
            'instagram' => $business->instagram,
            'notes' => $business->notes,
        ];

        return $instruction . "\n\n" .
            'Business: ' . json_encode($businessData) . "\n" .
            'Creator Profile: ' . json_encode($creatorData) . "\n" .
            'Recent Generated Messages: ' . json_encode($previousMessages);
    }

    private function buildAudienceSummary(CreatorProfile $profile)
    {
        $parts = [];

        if (!empty($profile->instagram_followers)) {
            $parts[] = 'Instagram: ' . number_format($profile->instagram_followers);
        }

        if (!empty($profile->tiktok_followers)) {
            $parts[] = 'TikTok: ' . number_format($profile->tiktok_followers);
        }

        if (!empty($profile->youtube_subscribers)) {
            $parts[] = 'YouTube: ' . number_format($profile->youtube_subscribers);
        }

        if (empty($parts)) {
            return 'an engaged local audience';
        }

        return implode(', ', $parts);
    }
}
