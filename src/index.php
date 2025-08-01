<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

echo "<pre>";
echo "Hello world\n";
echo microtime() . "\n";
echo "Environment variables:\n";
foreach ($_ENV as $key => $value) {
    echo "$key: $value\n";
}
echo "</pre>";

// Use prompt to contact OpenAI API, then upload each image in order
// Parse the JSON result from API, send back as response
