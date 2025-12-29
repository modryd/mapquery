# MapQuery POI Searcher

PHP library for searching Points of Interest (POI) by coordinates using Nominatim API.

## Installation

```bash
composer require mapquery/poi-searcher
```

## Usage

### Basic Example

```php
<?php

require 'vendor/autoload.php';

use MapQuery\POI\POISearcher;
use MapQuery\POI\ViewBox;

$searcher = new POISearcher();

// Create viewbox for Prague area (50°N, 14°E)
$viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

// Search for restaurants
$pois = $searcher->searchByViewBox($viewBox, ['amenity' => 'restaurant']);

foreach ($pois as $poi) {
    echo $poi->getName() . " - " . $poi->getDisplayName() . "\n";
    echo "Coordinates: " . $poi->getLatitude() . ", " . $poi->getLongitude() . "\n\n";
}
```

### Using Coordinates Directly

```php
<?php

require 'vendor/autoload.php';

use MapQuery\POI\POISearcher;

$searcher = new POISearcher();

// Search for cafes in Prague
$pois = $searcher->searchByCoordinates(
    14.5,  // minLon
    50.0,  // minLat
    14.6,  // maxLon
    50.1,  // maxLat
    ['amenity' => 'cafe']
);

foreach ($pois as $poi) {
    echo $poi->getName() . "\n";
}
```

### Custom Nominatim Client

```php
<?php

require 'vendor/autoload.php';

use MapQuery\POI\POISearcher;
use MapQuery\POI\NominatimClient;
use MapQuery\POI\ViewBox;

$client = new NominatimClient(
    'https://nominatim.openstreetmap.org',
    'My Application Name',
    15
);

$searcher = new POISearcher($client);
$viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);
$pois = $searcher->searchByViewBox($viewBox, ['amenity' => 'restaurant']);
```

## API Reference

### POISearcher

Main class for searching POIs.

#### Methods

- `searchByViewBox(ViewBox $viewBox, array $filters = []): array` - Search POIs using ViewBox object
- `searchByCoordinates(float $minLon, float $minLat, float $maxLon, float $maxLat, array $filters = []): array` - Search POIs using coordinates directly

#### Constructor

- `__construct(?NominatimClientInterface $client = null)` - Optional custom client implementation

### ViewBox

Represents a geographical bounding box.

#### Constructor

- `__construct(float $minLon, float $minLat, float $maxLon, float $maxLat)` - Creates a viewbox with validation

#### Methods

- `getMinLon(): float`
- `getMinLat(): float`
- `getMaxLon(): float`
- `getMaxLat(): float`
- `toNominatimFormat(): string` - Returns viewbox in Nominatim API format

### POI

Represents a single Point of Interest result.

#### Methods

- `getPlaceId(): int`
- `getLatitude(): float`
- `getLongitude(): float`
- `getName(): ?string`
- `getDisplayName(): string`
- `getClass(): string`
- `getType(): string`
- `getBoundingBox(): array`
- `toArray(): array` - Returns POI as associative array

## Filters

You can pass filters as an associative array. Common filters:

- `amenity` - Type of amenity (e.g., 'restaurant', 'cafe', 'hotel')
- `tourism` - Tourism-related POIs
- `shop` - Shopping-related POIs

See [Nominatim API documentation](https://nominatim.org/release-docs/develop/api/Search/) for full list of available filters.

## Error Handling

The library throws `MapQuery\POI\Exception\POISearchException` in case of errors:

```php
<?php

use MapQuery\POI\POISearcher;
use MapQuery\POI\ViewBox;
use MapQuery\POI\Exception\POISearchException;

try {
    $searcher = new POISearcher();
    $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);
    $pois = $searcher->searchByViewBox($viewBox, ['amenity' => 'restaurant']);
} catch (POISearchException $e) {
    echo "Error: " . $e->getMessage();
}
```

## Testing

The library includes comprehensive unit tests using PHPUnit. To run the tests:

```bash
composer install
vendor/bin/phpunit
```

## Requirements

- PHP 7.4 or higher
- Composer

## License

MIT

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

