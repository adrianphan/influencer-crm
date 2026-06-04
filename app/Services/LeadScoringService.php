<?php

namespace App\Services;

use App\Models\Business;

class LeadScoringService
{
    public function score(Business $business): array
    {
        $local = $this->scoreLocalRelevance($business);
        $fit = $this->scoreFoodLifestyleFit($business);
        $family = $this->scoreFamilyFriendlyFit($business);
        $visual = $this->scoreVisualContentPotential($business);
        $prQuality = $this->scorePrAgencyQuality($business);
        $similarity = $this->scoreSimilarityToSuccessfulCollaborations($business);

        $total = $local['score']
            + $fit['score']
            + $family['score']
            + $visual['score']
            + $prQuality['score']
            + $similarity['score'];

        $fitScore = (int) max(1, min(100, round($total)));

        $scoringNotes = implode("\n", [
            'Local relevance: ' . $local['score'] . '/20 - ' . $local['note'],
            'Food/lifestyle fit: ' . $fit['score'] . '/20 - ' . $fit['note'],
            'Family-friendly fit: ' . $family['score'] . '/15 - ' . $family['note'],
            'Visual content potential: ' . $visual['score'] . '/15 - ' . $visual['note'],
            'PR agency quality: ' . $prQuality['score'] . '/15 - ' . $prQuality['note'],
            'Similarity to successful collaborations: ' . $similarity['score'] . '/15 - ' . $similarity['note'],
            'Total fit score: ' . $fitScore . '/100',
        ]);

        return [
            'fit_score' => $fitScore,
            'scoring_notes' => $scoringNotes,
        ];
    }

    private function scoreLocalRelevance(Business $business): array
    {
        $city = strtolower((string) $business->city);
        $state = strtolower((string) $business->state);
        $notes = strtolower((string) $business->notes);

        $score = 8;

        if (strpos($city, 'las vegas') !== false || strpos($notes, 'las vegas') !== false) {
            $score = 20;
        } elseif ($city !== '' || $state !== '') {
            $score = 14;
        }

        return [
            'score' => $score,
            'note' => $score >= 20
                ? 'Strong local relevance to Las Vegas audience.'
                : ($score >= 14 ? 'Has local market details but not explicitly Las Vegas focused.' : 'Limited local context available.'),
        ];
    }

    private function scoreFoodLifestyleFit(Business $business): array
    {
        $text = strtolower(implode(' ', [
            (string) $business->name,
            (string) $business->category,
            (string) $business->notes,
        ]));

        $keywords = [
            'restaurant', 'coffee', 'cafe', 'dessert', 'bakery', 'food',
            'lifestyle', 'hospitality', 'travel', 'event', 'hotel',
        ];

        $matches = $this->countKeywordMatches($text, $keywords);
        $score = min(20, 6 + ($matches * 3));

        return [
            'score' => $score,
            'note' => $matches > 0
                ? 'Matches creator food/lifestyle niches with ' . $matches . ' relevant keyword hits.'
                : 'Limited direct alignment with food/lifestyle categories.',
        ];
    }

    private function scoreFamilyFriendlyFit(Business $business): array
    {
        $text = strtolower(implode(' ', [
            (string) $business->name,
            (string) $business->category,
            (string) $business->notes,
        ]));

        $keywords = [
            'family', 'kids', 'children', 'park', 'attraction', 'weekend',
            'brunch', 'community', 'activities',
        ];

        $matches = $this->countKeywordMatches($text, $keywords);
        $score = min(15, 4 + ($matches * 3));

        return [
            'score' => $score,
            'note' => $matches > 0
                ? 'Family-friendly relevance detected via ' . $matches . ' keyword hits.'
                : 'No strong family-specific indicators yet.',
        ];
    }

    private function scoreVisualContentPotential(Business $business): array
    {
        $text = strtolower(implode(' ', [
            (string) $business->name,
            (string) $business->category,
            (string) $business->notes,
        ]));

        $keywords = [
            'instagram', 'visual', 'aesthetic', 'dessert', 'cocktail',
            'event', 'travel', 'experience', 'photo', 'video',
        ];

        $matches = $this->countKeywordMatches($text, $keywords);
        $presenceBonus = (!empty($business->website) ? 2 : 0) + (!empty($business->instagram) ? 2 : 0);
        $score = min(15, 5 + ($matches * 2) + $presenceBonus);

        return [
            'score' => $score,
            'note' => 'Visual potential estimated from brand profile signals and media-friendly keywords.',
        ];
    }

    private function scorePrAgencyQuality(Business $business): array
    {
        if ($business->lead_type !== 'pr_agency') {
            return [
                'score' => 8,
                'note' => 'Neutral score for non-PR leads.',
            ];
        }

        $score = 6;

        if (!empty($business->website)) {
            $score += 3;
        }

        if (!empty($business->contact_name)) {
            $score += 2;
        }

        if (!empty($business->email)) {
            $score += 2;
        }

        if (!empty($business->agency_specialties)) {
            $score += 2;
        }

        $text = strtolower(implode(' ', [
            (string) $business->agency_specialties,
            (string) $business->client_types,
            (string) $business->category,
            (string) $business->notes,
        ]));

        $matches = $this->countKeywordMatches($text, ['hospitality', 'food', 'lifestyle', 'travel', 'influencer', 'creator']);
        $score += min(4, $matches);

        return [
            'score' => min(15, $score),
            'note' => 'PR quality based on completeness and relevant sector focus.',
        ];
    }

    private function scoreSimilarityToSuccessfulCollaborations(Business $business): array
    {
        $successful = Business::query()
            ->whereIn('status', ['Booked', 'Completed'])
            ->get(['id', 'lead_type', 'category', 'city']);

        if ($successful->isEmpty()) {
            return [
                'score' => 8,
                'note' => 'No successful-collaboration history yet. Using neutral baseline.',
            ];
        }

        $matches = 0;

        foreach ($successful as $past) {
            $isMatch = false;

            if (!empty($business->lead_type) && $past->lead_type === $business->lead_type) {
                $isMatch = true;
            }

            if (!empty($business->category) && !empty($past->category)
                && strtolower((string) $past->category) === strtolower((string) $business->category)) {
                $isMatch = true;
            }

            if (!empty($business->city) && !empty($past->city)
                && strtolower((string) $past->city) === strtolower((string) $business->city)) {
                $isMatch = true;
            }

            if ($isMatch) {
                $matches++;
            }
        }

        $ratio = $successful->count() > 0 ? $matches / $successful->count() : 0;
        $score = (int) min(15, max(3, round(3 + (12 * $ratio))));

        return [
            'score' => $score,
            'note' => 'Based on overlap with prior Booked/Completed leads (' . $matches . ' of ' . $successful->count() . ').',
        ];
    }

    private function countKeywordMatches(string $text, array $keywords): int
    {
        $matches = 0;

        foreach ($keywords as $keyword) {
            if ($keyword !== '' && strpos($text, $keyword) !== false) {
                $matches++;
            }
        }

        return $matches;
    }
}
