<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set error handling
if ($_ENV['ENV'] !== 'production') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    ini_set('display_errors', '0');
}

// Use prompt to contact OpenAI API, then upload each image in order
// Parse the JSON result from API, send back as response

Flight::route('/', function() {
    echo "<h1>Welcome</h1>";
    echo "<p>Use the <code>/json</code> endpoint to get a JSON response.</p>";
});

Flight::route('/json', function() {
    Flight::json(['hello' => 'world']);
});

Flight::start();
