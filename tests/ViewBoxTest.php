<?php

namespace MapQuery\POI\Tests;

use PHPUnit\Framework\TestCase;
use MapQuery\POI\ViewBox;
use InvalidArgumentException;

class ViewBoxTest extends TestCase
{
    public function testValidViewBoxCreation(): void
    {
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

        $this->assertEquals(14.5, $viewBox->getMinLon());
        $this->assertEquals(50.0, $viewBox->getMinLat());
        $this->assertEquals(14.6, $viewBox->getMaxLon());
        $this->assertEquals(50.1, $viewBox->getMaxLat());
    }

    public function testToNominatimFormat(): void
    {
        $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);
        $format = $viewBox->toNominatimFormat();

        $this->assertEquals('14.5,50,14.6,50.1', $format);
    }

    public function testInvalidMinLon(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('minLon must be between -180 and 180');

        new ViewBox(-181, 50.0, 14.6, 50.1);
    }

    public function testInvalidMaxLon(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('maxLon must be between -180 and 180');

        new ViewBox(14.5, 50.0, 181, 50.1);
    }

    public function testInvalidMinLat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('minLat must be between -90 and 90');

        new ViewBox(14.5, -91, 14.6, 50.1);
    }

    public function testInvalidMaxLat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('maxLat must be between -90 and 90');

        new ViewBox(14.5, 50.0, 14.6, 91);
    }

    public function testMinLonGreaterThanMaxLon(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('minLon must be less than maxLon');

        new ViewBox(14.6, 50.0, 14.5, 50.1);
    }

    public function testMinLatGreaterThanMaxLat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('minLat must be less than maxLat');

        new ViewBox(14.5, 50.1, 14.6, 50.0);
    }

    public function testMinLonEqualsMaxLon(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('minLon must be less than maxLon');

        new ViewBox(14.5, 50.0, 14.5, 50.1);
    }

    public function testMinLatEqualsMaxLat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('minLat must be less than maxLat');

        new ViewBox(14.5, 50.0, 14.6, 50.0);
    }

    public function testBoundaryValues(): void
    {
        $viewBox = new ViewBox(-180, -90, 180, 90);

        $this->assertEquals(-180, $viewBox->getMinLon());
        $this->assertEquals(-90, $viewBox->getMinLat());
        $this->assertEquals(180, $viewBox->getMaxLon());
        $this->assertEquals(90, $viewBox->getMaxLat());
    }
}
