<?php

require 'vendor/autoload.php';

use MapQuery\POI\POISearcher;
use MapQuery\POI\ViewBox;
use MapQuery\POI\Exception\POISearchException;

try {
    $searcher = new POISearcher();

    $viewBox = new ViewBox(14.5, 50.0, 14.6, 50.1);

    $pois = $searcher->searchByViewBox($viewBox, ['amenity' => 'restaurant']);

    echo "Found " . count($pois) . " restaurants:\n\n";

    foreach ($pois as $poi) {
        echo "Name: " . ($poi->getName() ?? 'N/A') . "\n";
        echo "Display Name: " . $poi->getDisplayName() . "\n";
        echo "Coordinates: " . $poi->getLatitude() . ", " . $poi->getLongitude() . "\n";
        echo "Type: " . $poi->getClass() . "/" . $poi->getType() . "\n";
        echo "---\n";
    }
} catch (POISearchException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}

