<?php
define('STYLE', 'osmde');

// Configure your cache directory
$cacheDir = __DIR__ . '/osm_cache'; // This will store tiles in a directory called 'osm_cache' in the same folder as the PHP script

// Ensure the cache directory exists
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

// URL pattern for OpenStreetMap tile server
$osmUrl = 'https://{s}.tile.openstreetmap.de/tiles/' . STYLE . '/{z}/{x}/{y}.png';

// Allowed subdomains
$subdomains = ['a', 'b', 'c'];

// Get x, y, z from the query parameters
$z = intval($_GET['z']);
$x = intval($_GET['x']);
$y = intval($_GET['y']);

// Generate a random subdomain
$subdomain = $subdomains[array_rand($subdomains)];

// Generate the filename for the cache
$tilePath = "$cacheDir/$z/$x";
$tileFile = "$tilePath/$y.png";

// Check if the tile is already cached
if (file_exists($tileFile)) {
    // Serve the cached file
    header('Content-Type: image/png');
    readfile($tileFile);
    exit;
}

// If not cached, download the tile from OpenStreetMap
$tileUrl = str_replace(['{s}', '{z}', '{x}', '{y}'], [$subdomain, $z, $x, $y], $osmUrl);

// Download the tile
$tileData = file_get_contents($tileUrl);

// Check if the download was successful
if ($tileData === false) {
    // Return a 404 error if the tile could not be downloaded
    header("HTTP/1.1 404 Not Found");
    echo "Tile not found";
    exit;
}

// Save the tile to the cache
if (!is_dir($tilePath)) {
    mkdir($tilePath, 0777, true);
}
file_put_contents($tileFile, $tileData);

// Serve the downloaded file
header('Content-Type: image/png');
echo $tileData;
exit;
