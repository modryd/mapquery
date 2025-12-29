<?php

namespace modryd\MapQuery;

class POI
{
    private int $placeId;
    private string $licence;
    private string $osmType;
    private int $osmId;
    private float $latitude;
    private float $longitude;
    private string $class;
    private string $type;
    private ?string $name;
    private string $displayName;
    private array $boundingBox;

    public function __construct(array $data)
    {
        $this->placeId = (int)$data['place_id'];
        $this->licence = $data['licence'] ?? '';
        $this->osmType = $data['osm_type'] ?? '';
        $this->osmId = (int)$data['osm_id'];
        $this->latitude = (float)$data['lat'];
        $this->longitude = (float)$data['lon'];
        $this->class = $data['class'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->name = $data['name'] ?? null;
        $this->displayName = $data['display_name'] ?? '';
        $this->boundingBox = $data['boundingbox'] ?? [];
    }

    public function getPlaceId(): int
    {
        return $this->placeId;
    }

    public function getLicence(): string
    {
        return $this->licence;
    }

    public function getOsmType(): string
    {
        return $this->osmType;
    }

    public function getOsmId(): int
    {
        return $this->osmId;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getBoundingBox(): array
    {
        return $this->boundingBox;
    }

    public function toArray(): array
    {
        return [
            'place_id' => $this->placeId,
            'licence' => $this->licence,
            'osm_type' => $this->osmType,
            'osm_id' => $this->osmId,
            'lat' => $this->latitude,
            'lon' => $this->longitude,
            'class' => $this->class,
            'type' => $this->type,
            'name' => $this->name,
            'display_name' => $this->displayName,
            'boundingbox' => $this->boundingBox
        ];
    }
}

