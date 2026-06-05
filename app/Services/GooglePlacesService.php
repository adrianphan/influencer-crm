<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    private const SEARCH_ENDPOINT = 'https://places.googleapis.com/v1/places:searchText';
    private const DETAILS_ENDPOINT = 'https://places.googleapis.com/v1/places';

    public function search(array $filters): array
    {
        $apiKey = config('services.google_places.api_key');

        if (empty($apiKey)) {
            return [];
        }

        $query = $this->buildSearchQuery($filters);

        try {
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'places.id,places.displayName,places.formattedAddress,places.rating,places.types',
            ])->post(self::SEARCH_ENDPOINT, [
                'textQuery' => $query,
                'maxResultCount' => 12,
            ]);

            if (!$response->successful()) {
                return [];
            }

            $results = data_get($response->json(), 'places', []);

            return collect($results)
                ->map(function ($item) use ($apiKey, $filters) {
                    $placeId = (string) ($item['id'] ?? '');
                    $details = $this->fetchPlaceDetails($placeId, $apiKey);

                    $leadType = $this->resolveLeadType($item, $filters['lead_type'] ?? null);
                    $category = $filters['category'] ?: $this->guessCategoryFromTypes($item['types'] ?? []);
                    $rating = isset($item['rating']) ? (float) $item['rating'] : null;

                    return [
                        'name' => (string) data_get($item, 'displayName.text', ''),
                        'lead_type' => $leadType,
                        'category' => $category,
                        'website' => $details['website'] ?? null,
                        'instagram' => null,
                        'email' => null,
                        'phone' => $details['phone'] ?? null,
                        'address' => (string) ($item['formattedAddress'] ?? ''),
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
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'nationalPhoneNumber,websiteUri',
            ])->get(self::DETAILS_ENDPOINT . '/' . rawurlencode($placeId));

            if (!$response->successful()) {
                return [];
            }

            $result = $response->json();

            return [
                'phone' => $result['nationalPhoneNumber'] ?? null,
                'website' => $result['websiteUri'] ?? null,
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
