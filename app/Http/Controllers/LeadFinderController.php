<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Services\GooglePlacesService;
use App\Services\LeadScoringService;
use Illuminate\Http\Request;

class LeadFinderController extends Controller
{
    public function index(Request $request, GooglePlacesService $googlePlacesService)
    {
        $filters = [
            'search_query' => $request->input('search_query'),
            'lead_type' => $request->input('lead_type'),
            'category' => $request->input('category'),
            'city' => $request->input('city'),
        ];

        $results = [];
        $hasSearch = collect($filters)->filter()->isNotEmpty();

        if ($hasSearch) {
            $results = collect($googlePlacesService->search($filters))
                ->map(function ($result) {
                    $result['already_in_crm'] = $this->findDuplicate($result) ? true : false;
                    return $result;
                })
                ->values()
                ->toArray();
        }

        return view('lead-finder.index', compact('filters', 'results', 'hasSearch'));
    }

    public function addToCrm(Request $request, LeadScoringService $leadScoringService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lead_type' => 'required|in:business,pr_agency',
            'category' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:5',
            'place_id' => 'nullable|string|max:255',
            'fit_score' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'city' => 'nullable|string|max:255',
        ]);

        $duplicate = $this->findDuplicate($validated);

        if ($duplicate) {
            return redirect('/businesses/' . $duplicate->id)
                ->with('error', 'That lead is already in your CRM.');
        }

        $notes = trim((string) ($validated['notes'] ?? ''));
        if (!empty($validated['rating'])) {
            $ratingText = 'Google rating: ' . number_format((float) $validated['rating'], 1) . '/5';
            $notes = $notes ? $notes . ' | ' . $ratingText : $ratingText;
        }

        $business = new Business([
            'name' => $validated['name'],
            'lead_type' => $validated['lead_type'],
            'category' => $validated['category'] ?? null,
            'website' => $validated['website'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'fit_score' => $validated['fit_score'] ?? null,
            'city' => $validated['city'] ?? null,
            'notes' => $notes ?: null,
            'status' => 'Lead Found',
        ]);

        $scoring = $leadScoringService->score($business);
        $business->fit_score = $scoring['fit_score'];
        $business->scoring_notes = $scoring['scoring_notes'];
        $business->save();

        return redirect('/businesses/' . $business->id)->with('success', 'Lead added to CRM.');
    }

    private function findDuplicate(array $lead): ?Business
    {
        $name = trim((string) ($lead['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        $website = trim((string) ($lead['website'] ?? ''));
        $city = trim((string) ($lead['city'] ?? ''));

        $query = Business::query()->where('name', $name);

        if ($website !== '') {
            $query->where('website', $website);
        } elseif ($city !== '') {
            $query->where('city', $city);
        }

        return $query->first();
    }
}
