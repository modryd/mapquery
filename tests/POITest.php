<?php

namespace modryd\MapQuery\Tests;

use PHPUnit\Framework\TestCase;
use modryd\MapQuery\POI;

class POITest extends TestCase
{
    public function testPOICreationWithFullData(): void
    {
        $data = [
            'place_id' => 123456,
            'licence' => 'Test Licence',
            'osm_type' => 'node',
            'osm_id' => 789012,
            'lat' => 50.0721691,
            'lon' => 14.5092312,
            'class' => 'amenity',
            'type' => 'restaurant',
            'name' => 'Test Restaurant',
            'display_name' => 'Test Restaurant, Prague',
            'boundingbox' => [50.0720795, 50.0722865, 14.5090710, 14.5093882]
        ];

        $poi = new POI($data);

        $this->assertEquals(123456, $poi->getPlaceId());
        $this->assertEquals('Test Licence', $poi->getLicence());
        $this->assertEquals('node', $poi->getOsmType());
        $this->assertEquals(789012, $poi->getOsmId());
        $this->assertEquals(50.0721691, $poi->getLatitude());
        $this->assertEquals(14.5092312, $poi->getLongitude());
        $this->assertEquals('amenity', $poi->getClass());
        $this->assertEquals('restaurant', $poi->getType());
        $this->assertEquals('Test Restaurant', $poi->getName());
        $this->assertEquals('Test Restaurant, Prague', $poi->getDisplayName());
        $this->assertEquals([50.0720795, 50.0722865, 14.5090710, 14.5093882], $poi->getBoundingBox());
    }

    public function testPOICreationWithMinimalData(): void
    {
        $data = [
            'place_id' => 123,
            'osm_id' => 456,
            'lat' => 50.0,
            'lon' => 14.0
        ];

        $poi = new POI($data);

        $this->assertEquals(123, $poi->getPlaceId());
        $this->assertEquals('', $poi->getLicence());
        $this->assertEquals('', $poi->getOsmType());
        $this->assertEquals(456, $poi->getOsmId());
        $this->assertEquals(50.0, $poi->getLatitude());
        $this->assertEquals(14.0, $poi->getLongitude());
        $this->assertEquals('', $poi->getClass());
        $this->assertEquals('', $poi->getType());
        $this->assertNull($poi->getName());
        $this->assertEquals('', $poi->getDisplayName());
        $this->assertEquals([], $poi->getBoundingBox());
    }

    public function testToArray(): void
    {
        $data = [
            'place_id' => 123456,
            'licence' => 'Test Licence',
            'osm_type' => 'node',
            'osm_id' => 789012,
            'lat' => 50.0721691,
            'lon' => 14.5092312,
            'class' => 'amenity',
            'type' => 'restaurant',
            'name' => 'Test Restaurant',
            'display_name' => 'Test Restaurant, Prague',
            'boundingbox' => [50.0720795, 50.0722865, 14.5090710, 14.5093882]
        ];

        $poi = new POI($data);
        $array = $poi->toArray();

        $this->assertEquals($data, $array);
    }

    public function testTypeCasting(): void
    {
        $data = [
            'place_id' => '123456',
            'osm_id' => '789012',
            'lat' => '50.0721691',
            'lon' => '14.5092312'
        ];

        $poi = new POI($data);

        $this->assertIsInt($poi->getPlaceId());
        $this->assertIsInt($poi->getOsmId());
        $this->assertIsFloat($poi->getLatitude());
        $this->assertIsFloat($poi->getLongitude());
    }
}

