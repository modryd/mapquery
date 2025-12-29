<?php
$url = "https://nominatim.openstreetmap.org/search?format=json&viewbox="
       .implode(',', [
         '14.5', '50.0',
         '14.6', '50.1'
       ])
       ."&bounded=1&amenity=restaurant";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'User-Agent: MapQuery Application'
    ]
]);

$data = file_get_contents($url, false, $context);
$pois = json_decode($data, true);
print_r($pois);