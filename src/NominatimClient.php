<?php

namespace modryd\MapQuery;

use modryd\MapQuery\Exception\POISearchException;

class NominatimClient implements POIClientInterface
{
    private string $baseUrl;
    private string $userAgent;
    private int $timeout;

    public function __construct(
        string $baseUrl = 'https://nominatim.openstreetmap.org',
        string $userAgent = 'modryd MapQuery',
        int $timeout = 10
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->userAgent = $userAgent;
        $this->timeout = $timeout;
    }

    public function search(ViewBox $viewBox, array $filters = []): array
    {
        $url = $this->buildUrl($viewBox, $filters);
        $context = $this->createStreamContext();

        $data = @file_get_contents($url, false, $context);

        if ($data === false) {
            $error = error_get_last();
            throw new POISearchException(
                'Failed to fetch data from Nominatim API: ' . ($error['message'] ?? 'Unknown error')
            );
        }

        $decoded = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new POISearchException(
                'Failed to decode JSON response: ' . json_last_error_msg()
            );
        }

        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    private function buildUrl(ViewBox $viewBox, array $filters): string
    {
        $params = [
            'format' => 'json',
            'viewbox' => $viewBox->toNominatimFormat(),
            'bounded' => '1'
        ];

        foreach ($filters as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $params[$key] = $value;
            }
        }

        return $this->baseUrl . '/search?' . http_build_query($params);
    }

    private function createStreamContext()
    {
        return stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: ' . $this->userAgent . "\r\n",
                'timeout' => $this->timeout
            ]
        ]);
    }
}

