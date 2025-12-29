<?php

namespace modryd\MapQuery;

use modryd\MapQuery\Exception\POISearchException;

class OverpassClient implements POIClientInterface
{
    private string $baseUrl;
    private string $userAgent;
    private int $timeout;

    public function __construct(
        string $baseUrl = 'https://overpass-api.de/api/interpreter',
        string $userAgent = 'MapQuery',
        int $timeout = 30
    ) {
        $this->baseUrl = $baseUrl;
        $this->userAgent = $userAgent;
        $this->timeout = $timeout;
    }

    public function search(ViewBox $viewBox, array $filters = []): array
    {
        $query = $this->buildQuery($viewBox, $filters);

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'User-Agent: ' . $this->userAgent,
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                'content' => 'data=' . urlencode($query),
                'timeout' => $this->timeout
            ]
        ];

        $context = stream_context_create($opts);
        $data = @file_get_contents($this->baseUrl, false, $context);

        if ($data === false) {
            $error = error_get_last();
            throw new POISearchException(
                'Failed to fetch data from Overpass API: ' . ($error['message'] ?? 'Unknown error')
            );
        }

        $decoded = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new POISearchException(
                'Failed to decode JSON response: ' . json_last_error_msg()
            );
        }

        if (!isset($decoded['elements']) || !is_array($decoded['elements'])) {
            return [];
        }

        return $this->convertOverpassToPOIFormat($decoded['elements']);
    }

    private function buildQuery(ViewBox $viewBox, array $filters = []): string
    {
        $south = $viewBox->getMinLat();
        $west = $viewBox->getMinLon();
        $north = $viewBox->getMaxLat();
        $east = $viewBox->getMaxLon();

        if (empty($filters)) {
            $query = '(node["amenity"];node["tourism"];node["shop"];way["amenity"];way["tourism"];way["shop"];)';
        } else {
            $queryParts = [];
            foreach ($filters as $key => $value) {
                if (is_string($key) && is_string($value)) {
                    $queryParts[] = sprintf('node["%s"="%s"];', $key, $value);
                    $queryParts[] = sprintf('way["%s"="%s"];', $key, $value);
                }
            }
            $query = '(' . implode('', $queryParts) . ')';
        }

        return sprintf(
            '[out:json][bbox:%s,%s,%s,%s];%s;out center;',
            $south,
            $west,
            $north,
            $east,
            $query
        );
    }


    private function convertOverpassToPOIFormat(array $elements): array
    {
        $pois = [];

        foreach ($elements as $element) {
            if (!isset($element['type']) || !isset($element['tags'])) {
                continue;
            }

            $tags = $element['tags'];
            $lat = $element['lat'] ?? ($element['center']['lat'] ?? null);
            $lon = $element['lon'] ?? ($element['center']['lon'] ?? null);

            if ($lat === null || $lon === null) {
                continue;
            }

            $poiData = [
                'place_id' => $element['id'] ?? 0,
                'licence' => 'Data © OpenStreetMap contributors, ODbL 1.0. http://osm.org/copyright',
                'osm_type' => $element['type'],
                'osm_id' => $element['id'] ?? 0,
                'lat' => (float)$lat,
                'lon' => (float)$lon,
                'class' => $tags['amenity'] ?? $tags['tourism'] ?? $tags['shop'] ?? '',
                'type' => $tags['amenity'] ?? $tags['tourism'] ?? $tags['shop'] ?? '',
                'name' => $tags['name'] ?? null,
                'display_name' => $this->buildDisplayName($tags, $lat, $lon),
                'boundingbox' => $this->buildBoundingBox($element, $lat, $lon)
            ];

            $pois[] = $poiData;
        }

        return $pois;
    }

    private function buildDisplayName(array $tags, float $lat, float $lon): string
    {
        $parts = [];

        if (isset($tags['name'])) {
            $parts[] = $tags['name'];
        }

        if (isset($tags['addr:street']) && isset($tags['addr:housenumber'])) {
            $parts[] = $tags['addr:housenumber'] . ', ' . $tags['addr:street'];
        } elseif (isset($tags['addr:street'])) {
            $parts[] = $tags['addr:street'];
        }

        if (empty($parts)) {
            $parts[] = sprintf('POI at %.6f, %.6f', $lat, $lon);
        }

        return implode(', ', $parts);
    }

    private function buildBoundingBox(array $element, float $lat, float $lon): array
    {
        if (isset($element['bounds'])) {
            return [
                $element['bounds']['minlat'],
                $element['bounds']['maxlat'],
                $element['bounds']['minlon'],
                $element['bounds']['maxlon']
            ];
        }

        $offset = 0.0001;
        return [
            $lat - $offset,
            $lat + $offset,
            $lon - $offset,
            $lon + $offset
        ];
    }
}

