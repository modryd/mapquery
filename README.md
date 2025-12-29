# MapQuery

PHP library for searching Points of Interest (POI) by coordinates using Overpass API.

## Installation

```bash
composer require modryd/mapquery
```

## Usage

### Basic Example

```php
<?php

require 'vendor/autoload.php';

use modryd\MapQuery\POISearcher;
use modryd\MapQuery\ViewBox;

$searcher = new POISearcher();

// Create viewbox for Prague area (50°N, 14°E)
$viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

// Search for all POIs (uses Overpass API - no need to specify amenity)
$pois = $searcher->searchByViewBox($viewBox);

foreach ($pois as $poi) {
    echo $poi->getName() . " - " . $poi->getDisplayName() . "\n";
    echo "Coordinates: " . $poi->getLatitude() . ", " . $poi->getLongitude() . "\n";
    echo "Type: " . $poi->getType() . "\n\n";
}
```


### Using Coordinates Directly

```php
<?php

require 'vendor/autoload.php';

use modryd\MapQuery\POISearcher;

$searcher = new POISearcher();
// or $searcher = new POISearcher(new modryd\MapQuery\NominatimClient());

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

use modryd\MapQuery\POISearcher;
use modryd\MapQuery\NominatimClient;
use modryd\MapQuery\ViewBox;

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

- `__construct(?modryd\MapQuery\POIClientInterface $client = null)` - Optional custom client implementation

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

## API Selection

The library uses **Overpass API** for all searches, which supports both filtered and unfiltered queries:

- **No filters (empty array or omitted)**: Returns all POIs (amenity, tourism, shop) in the viewbox
- **With filters**: Returns only POIs matching the specified filters

### Filters

Overpass API supports filtering by any OpenStreetMap tag. Common filters:

- `amenity` - Type of amenity (e.g., 'restaurant', 'cafe', 'hotel', 'parking')
- `tourism` - Tourism-related POIs (e.g., 'hotel', 'attraction', 'museum')
- `shop` - Shopping-related POIs (e.g., 'supermarket', 'bakery', 'clothes')

Example with filters:

```php
// Search for restaurants only
$pois = $searcher->searchByViewBox($viewBox, ['amenity' => 'restaurant']);

// Search for supermarkets
$pois = $searcher->searchByViewBox($viewBox, ['shop' => 'supermarket']);
```

See [Overpass API documentation](https://wiki.openstreetmap.org/wiki/Overpass_API) for more information about filtering.

## Error Handling

The library throws `modryd\MapQuery\Exception\POISearchException` in case of errors:

```php
<?php

use modryd\MapQuery\POISearcher;
use modryd\MapQuery\ViewBox;
use modryd\MapQuery\Exception\POISearchException;

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

