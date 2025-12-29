<?php

namespace modryd\MapQuery;

class ViewBox
{
    private float $minLon;
    private float $minLat;
    private float $maxLon;
    private float $maxLat;

    public function __construct(float $minLon, float $minLat, float $maxLon, float $maxLat)
    {
        $this->validateCoordinates($minLon, $minLat, $maxLon, $maxLat);
        $this->minLon = $minLon;
        $this->minLat = $minLat;
        $this->maxLon = $maxLon;
        $this->maxLat = $maxLat;
    }

    private function validateCoordinates(float $minLon, float $minLat, float $maxLon, float $maxLat): void
    {
        if ($minLon < -180 || $minLon > 180) {
            throw new \InvalidArgumentException('minLon must be between -180 and 180');
        }
        if ($maxLon < -180 || $maxLon > 180) {
            throw new \InvalidArgumentException('maxLon must be between -180 and 180');
        }
        if ($minLat < -90 || $minLat > 90) {
            throw new \InvalidArgumentException('minLat must be between -90 and 90');
        }
        if ($maxLat < -90 || $maxLat > 90) {
            throw new \InvalidArgumentException('maxLat must be between -90 and 90');
        }
        if ($minLon >= $maxLon) {
            throw new \InvalidArgumentException('minLon must be less than maxLon');
        }
        if ($minLat >= $maxLat) {
            throw new \InvalidArgumentException('minLat must be less than maxLat');
        }
    }

    public function getMinLon(): float
    {
        return $this->minLon;
    }

    public function getMinLat(): float
    {
        return $this->minLat;
    }

    public function getMaxLon(): float
    {
        return $this->maxLon;
    }

    public function getMaxLat(): float
    {
        return $this->maxLat;
    }

    public function toNominatimFormat(): string
    {
        return implode(',', [
            $this->minLon,
            $this->minLat,
            $this->maxLon,
            $this->maxLat
        ]);
    }
}

