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

Flight::route('/', function () {
    echo "<h1>Welcome</h1>";
    echo "<p>Use the <code>/json</code> endpoint to get a JSON response.</p>";
});

Flight::route('/json', function () {
    Flight::json(['hello' => 'world']);
});

Flight::route('/image', function () {
    // Use prompt to contact OpenAI API, then upload each image in order
    // Parse the JSON result from API, send back as response

    $prompt = file_get_contents(__DIR__ . '/../Prompt.md');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/responses");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "model" => "gpt-4.1-nano",
        "input" => $prompt,
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $_ENV['OPENAI_API_KEY']
    ]);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);
});

Flight::start();
