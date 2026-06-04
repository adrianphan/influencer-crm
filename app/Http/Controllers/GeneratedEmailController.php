<?php

namespace App\Http\Controllers;

use App\Models\GeneratedEmail;
use App\Services\GmailDraftService;

class GeneratedEmailController extends Controller
{
    public function show(GeneratedEmail $generatedEmail)
    {
        return view('generated-emails.show', compact('generatedEmail'));
    }

    public function destroy(GeneratedEmail $generatedEmail)
    {
        $businessId = $generatedEmail->business_id;
        $generatedEmail->delete();

        return redirect('/businesses/' . $businessId)->with('success', 'Generated message deleted.');
    }

    public function createGmailDraft(GeneratedEmail $generatedEmail, GmailDraftService $gmailDraftService)
    {
        if ($generatedEmail->draft_id) {
            return redirect('/generated-emails/' . $generatedEmail->id)
                ->with('success', 'Gmail draft already created for this message.');
        }

        $result = $gmailDraftService->createDraft($generatedEmail);

        $generatedEmail->update([
            'draft_id' => $result['draft_id'],
            'draft_created_at' => $result['draft_created_at'],
        ]);

        if (($result['provider'] ?? 'local') === 'gmail') {
            return redirect('/generated-emails/' . $generatedEmail->id)
                ->with('success', 'Gmail draft created. Review in Gmail and send manually.');
        }

        return redirect('/generated-emails/' . $generatedEmail->id)
            ->with('success', 'Draft prepared in local fallback mode. Connect Gmail token to create real Gmail drafts.');
    }
}
