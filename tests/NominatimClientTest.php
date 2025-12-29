<?php

namespace modryd\MapQuery\Tests;

use PHPUnit\Framework\TestCase;
use modryd\MapQuery\NominatimClient;
use modryd\MapQuery\ViewBox;
use modryd\MapQuery\Exception\POISearchException;

class NominatimClientTest extends TestCase
{
    public function testConstructorWithDefaults(): void
    {
        $client = new NominatimClient();

        $this->assertInstanceOf(NominatimClient::class, $client);
    }

    public function testConstructorWithCustomValues(): void
    {
        $client = new NominatimClient(
            'https://custom.example.com',
            'Custom User Agent',
            15
        );

        $this->assertInstanceOf(NominatimClient::class, $client);
    }

    public function testBuildUrlWithFilters(): void
    {
        $client = new NominatimClient('https://test.example.com');
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('buildUrl');
        $method->setAccessible(true);

        $url = $method->invoke($client, $viewBox, ['amenity' => 'restaurant']);

        $this->assertStringContainsString('https://test.example.com/search', $url);
        $this->assertStringContainsString('format=json', $url);
        $this->assertStringContainsString('viewbox=', $url);
        $this->assertStringContainsString('14.5', $url);
        $this->assertStringContainsString('50', $url);
        $this->assertStringContainsString('14.6', $url);
        $this->assertStringContainsString('50.1', $url);
        $this->assertStringContainsString('bounded=1', $url);
        $this->assertStringContainsString('amenity=restaurant', $url);
    }

    public function testBuildUrlWithoutFilters(): void
    {
        $client = new NominatimClient('https://test.example.com');
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('buildUrl');
        $method->setAccessible(true);

        $url = $method->invoke($client, $viewBox, []);

        $this->assertStringContainsString('https://test.example.com/search', $url);
        $this->assertStringContainsString('format=json', $url);
        $this->assertStringContainsString('viewbox=', $url);
        $this->assertStringContainsString('bounded=1', $url);
        $this->assertStringNotContainsString('amenity', $url);
    }

    public function testCreateStreamContext(): void
    {
        $client = new NominatimClient('https://test.example.com', 'Test Agent', 20);

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('createStreamContext');
        $method->setAccessible(true);

        $context = $method->invoke($client);

        $this->assertIsResource($context);
    }

    public function testSearchWithInvalidUrlThrowsException(): void
    {
        $client = new NominatimClient('https://invalid-url-that-does-not-exist-12345.com', 'Test Agent', 1);
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $this->expectException(POISearchException::class);
        $this->expectExceptionMessage('Failed to fetch data from Nominatim API');

        $client->search($viewBox);
    }
}
