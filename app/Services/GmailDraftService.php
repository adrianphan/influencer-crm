<?php

namespace App\Services;

use App\Models\GeneratedEmail;
use Illuminate\Support\Facades\Http;

class GmailDraftService
{
    public function createDraft(GeneratedEmail $generatedEmail)
    {
        $accessToken = config('services.gmail.access_token');

        if (empty($accessToken)) {
            return [
                'draft_id' => 'local_draft_' . now()->format('YmdHis') . '_' . $generatedEmail->id,
                'draft_created_at' => now(),
                'provider' => 'local',
            ];
        }

        $toEmail = $generatedEmail->business->email ?: 'draft@example.com';
        $subject = $generatedEmail->subject;

        $rawMessage = "To: {$toEmail}\r\n" .
            "Subject: {$subject}\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-Type: text/plain; charset=UTF-8\r\n\r\n" .
            $generatedEmail->body;

        $encoded = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');

        try {
            $response = Http::withToken($accessToken)
                ->post('https://gmail.googleapis.com/gmail/v1/users/me/drafts', [
                    'message' => [
                        'raw' => $encoded,
                    ],
                ]);

            if (!$response->successful()) {
                return [
                    'draft_id' => 'local_draft_' . now()->format('YmdHis') . '_' . $generatedEmail->id,
                    'draft_created_at' => now(),
                    'provider' => 'local',
                ];
            }

            return [
                'draft_id' => (string) data_get($response->json(), 'id'),
                'draft_created_at' => now(),
                'provider' => 'gmail',
            ];
        } catch (\Throwable $e) {
            return [
                'draft_id' => 'local_draft_' . now()->format('YmdHis') . '_' . $generatedEmail->id,
                'draft_created_at' => now(),
                'provider' => 'local',
            ];
        }
    }
}
