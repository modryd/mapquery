<?php

namespace MapQuery\POI\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MapQuery\POI\POISearcher;
use MapQuery\POI\NominatimClientInterface;
use MapQuery\POI\ViewBox;
use MapQuery\POI\POI;
use MapQuery\POI\Exception\POISearchException;

class POISearcherTest extends TestCase
{
    public function testConstructorWithDefaultClient(): void
    {
        $searcher = new POISearcher();

        $this->assertInstanceOf(POISearcher::class, $searcher);
    }

    public function testConstructorWithCustomClient(): void
    {
        $client = $this->createMock(NominatimClientInterface::class);
        $searcher = new POISearcher($client);

        $this->assertInstanceOf(POISearcher::class, $searcher);
    }

    public function testSearchByViewBox(): void
    {
        $mockData = [
            [
                'place_id' => 123,
                'osm_id' => 456,
                'lat' => 50.0,
                'lon' => 14.0,
                'class' => 'amenity',
                'type' => 'restaurant',
                'name' => 'Test Restaurant',
                'display_name' => 'Test Restaurant, Prague'
            ]
        ];

        $client = $this->createMock(NominatimClientInterface::class);
        $client->expects($this->once())
            ->method('search')
            ->willReturn($mockData);

        $searcher = new POISearcher($client);
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $pois = $searcher->searchByViewBox($viewBox, ['amenity' => 'restaurant']);

        $this->assertCount(1, $pois);
        $this->assertInstanceOf(POI::class, $pois[0]);
        $this->assertEquals('Test Restaurant', $pois[0]->getName());
    }

    public function testSearchByViewBoxWithEmptyResults(): void
    {
        $client = $this->createMock(NominatimClientInterface::class);
        $client->expects($this->once())
            ->method('search')
            ->willReturn([]);

        $searcher = new POISearcher($client);
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $pois = $searcher->searchByViewBox($viewBox);

        $this->assertIsArray($pois);
        $this->assertCount(0, $pois);
    }

    public function testSearchByViewBoxWithMultipleResults(): void
    {
        $mockData = [
            [
                'place_id' => 123,
                'osm_id' => 456,
                'lat' => 50.0,
                'lon' => 14.0,
                'name' => 'Restaurant 1'
            ],
            [
                'place_id' => 789,
                'osm_id' => 101112,
                'lat' => 50.1,
                'lon' => 14.1,
                'name' => 'Restaurant 2'
            ]
        ];

        $client = $this->createMock(NominatimClientInterface::class);
        $client->expects($this->once())
            ->method('search')
            ->willReturn($mockData);

        $searcher = new POISearcher($client);
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $pois = $searcher->searchByViewBox($viewBox);

        $this->assertCount(2, $pois);
        $this->assertEquals('Restaurant 1', $pois[0]->getName());
        $this->assertEquals('Restaurant 2', $pois[1]->getName());
    }

    public function testSearchByCoordinates(): void
    {
        $mockData = [
            [
                'place_id' => 123,
                'osm_id' => 456,
                'lat' => 50.0,
                'lon' => 14.0,
                'name' => 'Test Restaurant'
            ]
        ];

        $client = $this->createMock(NominatimClientInterface::class);
        $client->expects($this->once())
            ->method('search')
            ->willReturn($mockData);

        $searcher = new POISearcher($client);

        $pois = $searcher->searchByCoordinates(14.5, 50.0, 14.6, 50.1, ['amenity' => 'restaurant']);

        $this->assertCount(1, $pois);
        $this->assertInstanceOf(POI::class, $pois[0]);
    }

    public function testSearchByViewBoxThrowsPOISearchException(): void
    {
        $client = $this->createMock(NominatimClientInterface::class);
        $client->expects($this->once())
            ->method('search')
            ->willThrowException(new POISearchException('API Error'));

        $searcher = new POISearcher($client);
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $this->expectException(POISearchException::class);
        $this->expectExceptionMessage('API Error');

        $searcher->searchByViewBox($viewBox);
    }

    public function testSearchByViewBoxHandlesUnexpectedException(): void
    {
        $client = $this->createMock(NominatimClientInterface::class);
        $client->expects($this->once())
            ->method('search')
            ->willThrowException(new \RuntimeException('Unexpected error'));

        $searcher = new POISearcher($client);
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $this->expectException(POISearchException::class);
        $this->expectExceptionMessage('Unexpected error during POI search');

        $searcher->searchByViewBox($viewBox);
    }

    public function testSearchByViewBoxFiltersNonArrayResults(): void
    {
        $mockData = [
            [
                'place_id' => 123,
                'osm_id' => 456,
                'lat' => 50.0,
                'lon' => 14.0
            ],
            'invalid',
            null,
            123
        ];

        $client = $this->createMock(NominatimClientInterface::class);
        $client->expects($this->once())
            ->method('search')
            ->willReturn($mockData);

        $searcher = new POISearcher($client);
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $pois = $searcher->searchByViewBox($viewBox);

        $this->assertCount(1, $pois);
        $this->assertInstanceOf(POI::class, $pois[0]);
    }
}
