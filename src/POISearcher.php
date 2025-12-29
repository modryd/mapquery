<?php

namespace MapQuery\POI;

use MapQuery\POI\Exception\POISearchException;

class POISearcher
{
    private NominatimClientInterface $client;

    public function __construct(?NominatimClientInterface $client = null)
    {
        $this->client = $client ?? new NominatimClient();
    }

    public function searchByViewBox(ViewBox $viewBox, array $filters = []): array
    {
        try {
            $results = $this->client->search($viewBox, $filters);
            return $this->mapResultsToPOIs($results);
        } catch (\Exception $e) {
            if ($e instanceof POISearchException) {
                throw $e;
            }
            throw new POISearchException('Unexpected error during POI search: ' . $e->getMessage(), 0, $e);
        }
    }

    public function searchByCoordinates(
        float $minLon,
        float $minLat,
        float $maxLon,
        float $maxLat,
        array $filters = []
    ): array {
        $viewBox = new ViewBox($minLon, $minLat, $maxLon, $maxLat);
        return $this->searchByViewBox($viewBox, $filters);
    }

    private function mapResultsToPOIs(array $results): array
    {
        $pois = [];
        foreach ($results as $result) {
            if (is_array($result)) {
                $pois[] = new POI($result);
            }
        }
        return $pois;
    }
}

