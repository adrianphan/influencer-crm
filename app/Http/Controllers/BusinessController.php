<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\GeneratedEmail;
use App\Models\CreatorProfile;
use App\Models\Interaction;
use App\Services\AiGenerationService;
use App\Services\LeadScoringService;
use Illuminate\Support\Carbon;


class BusinessController extends Controller
{
    public function index()
{
    $businessStatuses = [
        'Lead Found',
        'Outreach Sent',
        'Interested',
        'Booked',
        'Content Created',
        'Posted',
        'Paid',
        'Completed',
        'Rejected',
    ];

    $prAgencyStatuses = [
        'Lead Found',
        'Outreach Sent',
        'Intro Call',
        'Added to Influencer List',
        'Receiving Opportunities',
        'Booked',
        'Content Created',
        'Posted',
        'Paid',
        'Completed',
        'Inactive',
        'Rejected',
    ];

    $statuses = array_values(array_unique(array_merge($businessStatuses, $prAgencyStatuses)));

    $statusFilter = request('status');
    $categoryFilter = request('category');
    $followUpsDueOnly = request('follow_ups_due_only');
    $leadTypeFilter = request('lead_type');

    $buildPipelineQuery = function ($leadType) use ($statusFilter, $categoryFilter, $followUpsDueOnly, $leadTypeFilter) {
        $query = Business::query()->where('lead_type', $leadType);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        if ($followUpsDueOnly) {
            $query->whereNotNull('follow_up_at')
                ->whereDate('follow_up_at', '<=', now()->toDateString());
        }

        if ($leadTypeFilter) {
            $query->where('lead_type', $leadTypeFilter);
        }

        return $query;
    };

    $directBusinessesByStatus = $buildPipelineQuery('business')->latest()
        ->get()
        ->groupBy('status');

    $prAgencyBusinessesByStatus = $buildPipelineQuery('pr_agency')->latest()
        ->get()
        ->groupBy('status');

    $followUpsDue = Business::whereNotNull('follow_up_at')
        ->whereDate('follow_up_at', '<=', now()->toDateString())
        ->orderBy('follow_up_at')
        ->get();

    $categories = Business::whereNotNull('category')
        ->where('category', '!=', '')
        ->select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    $totalLeads = Business::count();
    $outreachSentCount = Business::where('status', 'Outreach Sent')->count();
    $repliesCount = Interaction::where('direction', 'inbound')->count();
    $interestedCount = Business::where('status', 'Interested')->count();
    $bookedCount = Business::where('status', 'Booked')->count();
    $contentCreatedCount = Business::where('status', 'Content Created')->count();
    $postedCount = Business::where('status', 'Posted')->count();
    $paidCount = Business::where('status', 'Paid')->count();
    $completedCount = Business::where('status', 'Completed')->count();
    $followUpsDueCount = $followUpsDue->count();

    $startMonth = now()->startOfMonth()->subMonths(5);
    $monthKeys = collect(range(0, 5))->map(function ($offset) use ($startMonth) {
        return $startMonth->copy()->addMonths($offset);
    });

    $leadsByMonthRaw = Business::whereDate('created_at', '>=', $startMonth)
        ->get(['created_at'])
        ->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m');
        })
        ->map->count();

    $leadsByMonth = $monthKeys->map(function ($month) use ($leadsByMonthRaw) {
        $key = $month->format('Y-m');
        return [
            'label' => $month->format('M Y'),
            'value' => (int) ($leadsByMonthRaw[$key] ?? 0),
        ];
    })->toArray();

    $bookingsByMonthRaw = Business::whereNotNull('booking_date')
        ->whereDate('booking_date', '>=', $startMonth->toDateString())
        ->get(['booking_date'])
        ->groupBy(function ($item) {
            return Carbon::parse($item->booking_date)->format('Y-m');
        })
        ->map->count();

    $bookingsByMonth = $monthKeys->map(function ($month) use ($bookingsByMonthRaw) {
        $key = $month->format('Y-m');
        return [
            'label' => $month->format('M Y'),
            'value' => (int) ($bookingsByMonthRaw[$key] ?? 0),
        ];
    })->toArray();

    $topCategories = Business::whereNotNull('category')
        ->where('category', '!=', '')
        ->get(['category'])
        ->groupBy('category')
        ->map->count()
        ->sortDesc()
        ->take(5)
        ->map(function ($count, $category) {
            return ['label' => $category, 'value' => (int) $count];
        })
        ->values()
        ->toArray();

    $topPrAgencies = Business::where('lead_type', 'pr_agency')
        ->whereNotNull('name')
        ->get(['name'])
        ->groupBy('name')
        ->map->count()
        ->sortDesc()
        ->take(5)
        ->map(function ($count, $name) {
            return ['label' => $name, 'value' => (int) $count];
        })
        ->values()
        ->toArray();

    $creatorProfile = CreatorProfile::query()->first();
    $instagramFollowers = (int) ($creatorProfile->instagram_followers ?? 0);
    $tiktokFollowers = (int) ($creatorProfile->tiktok_followers ?? 0);
    $youtubeSubscribers = (int) ($creatorProfile->youtube_subscribers ?? 0);
    $totalAudience = $instagramFollowers + $tiktokFollowers + $youtubeSubscribers;

    return view('businesses.index', compact(
        'statuses',
        'businessStatuses',
        'prAgencyStatuses',
        'directBusinessesByStatus',
        'prAgencyBusinessesByStatus',
        'followUpsDue',
        'categories',
        'statusFilter',
        'categoryFilter',
        'followUpsDueOnly',
        'leadTypeFilter',
        'totalLeads',
        'outreachSentCount',
        'repliesCount',
        'interestedCount',
        'bookedCount',
        'contentCreatedCount',
        'postedCount',
        'paidCount',
        'completedCount',
        'followUpsDueCount',
        'leadsByMonth',
        'bookingsByMonth',
        'topCategories',
        'topPrAgencies',
        'creatorProfile',
        'instagramFollowers',
        'tiktokFollowers',
        'youtubeSubscribers',
        'totalAudience'
    ));
}

public function create()
{
    return view('businesses.create');
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, LeadScoringService $leadScoringService)
    {
         $validated = $request->validate([
        'name' => 'required',
        'lead_type' => 'required|in:business,pr_agency',
        'category' => 'nullable',
        'website' => 'nullable',
        'instagram' => 'nullable',
        'email' => 'nullable',
        'pr_contact_role' => 'nullable',
        'agency_specialties' => 'nullable',
        'client_types' => 'nullable',
        'roster_status' => 'nullable',
        'media_kit_sent_at' => 'nullable|date',
        'phone' => 'nullable',
        'address' => 'nullable',
        'city' => 'nullable',
        'state' => 'nullable',
        'contact_source' => 'nullable',
        'collab_value' => 'nullable|numeric',
        'deliverables' => 'nullable',
        'compensation' => 'nullable|numeric',
        'booking_date' => 'nullable|date',
        'posting_date' => 'nullable|date',
        'posted_url' => 'nullable',
        'payment_status' => 'nullable|in:Pending,Partially Paid,Paid',
        'contact_name' => 'nullable',
        'notes' => 'nullable',
    ]);

    if (empty($validated['status'])) {
        $validated['status'] = 'Lead Found';
    }

    $business = new Business($validated);
    $scoring = $leadScoringService->score($business);
    $business->fit_score = $scoring['fit_score'];
    $business->scoring_notes = $scoring['scoring_notes'];
    $business->save();

    return redirect('/businesses')->with('success', 'Business created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function show(Business $business)
{
    $business->load([
        'generatedEmails' => function ($query) {
            $query->latest();
        },
        'interactions' => function ($query) {
            $query->orderBy('occurred_at', 'desc');
        },
    ]);

    return view('businesses.show', compact('business'));
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function update(Request $request, Business $business, LeadScoringService $leadScoringService)
{
    $validated = $request->validate([
        'name' => 'required',
        'status' => 'required',
        'lead_type' => 'required|in:business,pr_agency',
        'contact_name' => 'nullable',
        'pr_contact_role' => 'nullable',
        'email' => 'nullable',
        'website' => 'nullable',
        'instagram' => 'nullable',
        'agency_specialties' => 'nullable',
        'client_types' => 'nullable',
        'roster_status' => 'nullable',
        'media_kit_sent_at' => 'nullable|date',
        'phone' => 'nullable',
        'address' => 'nullable',
        'city' => 'nullable',
        'state' => 'nullable',
        'contact_source' => 'nullable',
        'collab_value' => 'nullable|numeric',
        'deliverables' => 'nullable',
        'compensation' => 'nullable|numeric',
        'booking_date' => 'nullable|date',
        'posting_date' => 'nullable|date',
        'posted_url' => 'nullable',
        'payment_status' => 'nullable|in:Pending,Partially Paid,Paid',
        'last_contacted_at' => 'nullable|date',
        'follow_up_at' => 'nullable|date',
        'notes' => 'nullable',
    ]);

    $business->fill($validated);
    $scoring = $leadScoringService->score($business);
    $business->fit_score = $scoring['fit_score'];
    $business->scoring_notes = $scoring['scoring_notes'];
    $business->save();

    return redirect('/businesses/' . $business->id)->with('success', 'Business updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
public function generateEmail(Business $business, AiGenerationService $aiGenerationService)
{
    $creator = $this->getCreatorProfile();
    $generated = $aiGenerationService->generateOutreachEmail($business, $creator);

    $generatedEmail = GeneratedEmail::create([
        'business_id' => $business->id,
        'type' => 'outreach',
        'subject' => $generated['subject'],
        'body' => $generated['body'],
    ]);

    $this->logGeneratedInteractionIfRequested($business, 'email', $generatedEmail);

    return redirect('/generated-emails/' . $generatedEmail->id)->with('success', 'Outreach email generated.');
}

public function generateDm(Business $business, AiGenerationService $aiGenerationService)
{
    $creator = $this->getCreatorProfile();
    $generated = $aiGenerationService->generateInstagramDm($business, $creator);

    $generatedEmail = GeneratedEmail::create([
        'business_id' => $business->id,
        'type' => 'instagram_dm',
        'subject' => $generated['subject'],
        'body' => $generated['body'],
    ]);

    $this->logGeneratedInteractionIfRequested($business, 'dm', $generatedEmail);

    return redirect('/generated-emails/' . $generatedEmail->id)->with('success', 'Instagram DM generated.');
}

public function generateFollowUp(Business $business, AiGenerationService $aiGenerationService)
{
    $creator = $this->getCreatorProfile();
    $generated = $aiGenerationService->generateFollowUp($business, $creator);

    $generatedEmail = GeneratedEmail::create([
        'business_id' => $business->id,
        'type' => 'follow_up',
        'subject' => $generated['subject'],
        'body' => $generated['body'],
    ]);

    $this->logGeneratedInteractionIfRequested($business, 'email', $generatedEmail);

    return redirect('/generated-emails/' . $generatedEmail->id)->with('success', 'Follow-up generated.');
}

public function generatePrOutreach(Business $business, AiGenerationService $aiGenerationService)
{
    if ($business->lead_type !== 'pr_agency') {
        abort(404);
    }

    $creator = $this->getCreatorProfile();
    $generated = $aiGenerationService->generatePrOutreach($business, $creator);

    $generatedEmail = GeneratedEmail::create([
        'business_id' => $business->id,
        'type' => 'pr_agency_outreach',
        'subject' => $generated['subject'],
        'body' => $generated['body'],
    ]);

    $this->logGeneratedInteractionIfRequested($business, 'email', $generatedEmail);

    return redirect('/generated-emails/' . $generatedEmail->id)->with('success', 'PR agency outreach generated.');
}

public function generatePrFollowUp(Business $business, AiGenerationService $aiGenerationService)
{
    if ($business->lead_type !== 'pr_agency') {
        abort(404);
    }

    $creator = $this->getCreatorProfile();
    $generated = $aiGenerationService->generatePrFollowUp($business, $creator);

    $generatedEmail = GeneratedEmail::create([
        'business_id' => $business->id,
        'type' => 'pr_agency_follow_up',
        'subject' => $generated['subject'],
        'body' => $generated['body'],
    ]);

    $this->logGeneratedInteractionIfRequested($business, 'email', $generatedEmail);

    return redirect('/generated-emails/' . $generatedEmail->id)->with('success', 'PR agency follow-up generated.');
}

private function getCreatorProfile()
{
    return CreatorProfile::query()->first() ?? new CreatorProfile([
        'name' => 'Marie Alvarez',
        'display_name' => 'Marie',
        'instagram_handle' => '@mrsmariealvarezp',
        'instagram_followers' => 45000,
        'tiktok_handle' => '@mrsmariealvarezp',
        'tiktok_followers' => 12000,
        'youtube_handle' => '@mrsmariealvarezp',
        'youtube_subscribers' => 5000,
        'location' => 'Las Vegas',
        'niche' => 'food, lifestyle, family, restaurants, events, travel',
        'bio' => 'I create local Las Vegas content across food, lifestyle, family, restaurants, hospitality, events, and travel.',
        'media_kit_url' => null,
        'email_signature' => "Best,\nMarie",
        'audience_notes' => 'Las Vegas audience that engages with food, family activities, events, and hospitality recommendations.',
    ]);
}

private function logGeneratedInteractionIfRequested(Business $business, $type, GeneratedEmail $generatedEmail)
{
    if (!request()->boolean('log_interaction', true)) {
        return;
    }

    Interaction::create([
        'business_id' => $business->id,
        'type' => $type,
        'direction' => 'outbound',
        'content' => "Subject: {$generatedEmail->subject}\n\n{$generatedEmail->body}",
        'occurred_at' => now(),
    ]);
}
 }
