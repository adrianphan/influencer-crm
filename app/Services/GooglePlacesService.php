<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    public function search(array $filters): array
    {
        $apiKey = config('services.google_places.api_key');

        if (empty($apiKey)) {
            return [];
        }

        $query = $this->buildSearchQuery($filters);

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
                'query' => $query,
                'key' => $apiKey,
            ]);

            if (!$response->successful()) {
                return [];
            }

            $results = data_get($response->json(), 'results', []);

            return collect($results)
                ->take(12)
                ->map(function ($item) use ($apiKey, $filters) {
                    $placeId = (string) ($item['place_id'] ?? '');
                    $details = $this->fetchPlaceDetails($placeId, $apiKey);

                    $leadType = $this->resolveLeadType($item, $filters['lead_type'] ?? null);
                    $category = $filters['category'] ?: $this->guessCategoryFromTypes($item['types'] ?? []);
                    $rating = isset($item['rating']) ? (float) $item['rating'] : null;

                    return [
                        'name' => (string) ($item['name'] ?? ''),
                        'lead_type' => $leadType,
                        'category' => $category,
                        'website' => $details['website'] ?? null,
                        'instagram' => null,
                        'email' => null,
                        'phone' => $details['phone'] ?? null,
                        'address' => (string) ($item['formatted_address'] ?? ''),
                        'rating' => $rating,
                        'place_id' => $placeId,
                        'city' => $filters['city'] ?: 'Las Vegas',
                        'notes' => $this->buildNotes($rating, $item['types'] ?? []),
                    ];
                })
                ->filter(function ($lead) {
                    return !empty($lead['name']);
                })
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function buildSearchQuery(array $filters): string
    {
        $query = trim((string) ($filters['search_query'] ?? ''));

        if (!empty($query)) {
            return $query;
        }

        $city = trim((string) ($filters['city'] ?? 'Las Vegas'));
        $category = trim((string) ($filters['category'] ?? ''));
        $leadType = $filters['lead_type'] ?? null;

        if ($leadType === 'pr_agency' && empty($category)) {
            $category = 'PR agencies';
        }

        if (empty($category)) {
            $category = 'restaurants';
        }

        return trim($city . ' ' . $category);
    }

    private function fetchPlaceDetails(string $placeId, string $apiKey): array
    {
        if ($placeId === '') {
            return [];
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'fields' => 'formatted_phone_number,website',
                'key' => $apiKey,
            ]);

            if (!$response->successful()) {
                return [];
            }

            $result = data_get($response->json(), 'result', []);

            return [
                'phone' => $result['formatted_phone_number'] ?? null,
                'website' => $result['website'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function resolveLeadType(array $item, ?string $requestedLeadType): string
    {
        if (in_array($requestedLeadType, ['business', 'pr_agency'], true)) {
            return $requestedLeadType;
        }

        $types = collect($item['types'] ?? [])->map(function ($type) {
            return (string) $type;
        })->all();

        $prHints = ['marketing_agency', 'advertising_agency', 'public_relations'];

        foreach ($prHints as $hint) {
            if (in_array($hint, $types, true)) {
                return 'pr_agency';
            }
        }

        return 'business';
    }

    private function guessCategoryFromTypes(array $types): string
    {
        if (empty($types)) {
            return 'Business';
        }

        return ucwords(str_replace('_', ' ', (string) $types[0]));
    }

    private function buildNotes(?float $rating, array $types): string
    {
        $parts = [];

        if (!is_null($rating)) {
            $parts[] = 'Google rating: ' . number_format($rating, 1) . '/5';
        }

        if (!empty($types)) {
            $parts[] = 'Google types: ' . implode(', ', array_map(function ($type) {
                return str_replace('_', ' ', (string) $type);
            }, $types));
        }

        return implode(' | ', $parts);
    }
}
