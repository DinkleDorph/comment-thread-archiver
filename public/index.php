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

// Set session lifetime
ini_set('session.cookie_lifetime', 2592000); // 30 days
ini_set('session.gc_maxlifetime', 2592000);
session_start();

// Set up Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Src/Views');
$twig = new \Twig\Environment($loader);

// ---------- Routes ----------

Flight::route('/', function () use ($twig) {
    echo $twig->render('index.twig', [
        'ENV' => $_ENV['ENV'],
        'OPENAI_API_KEY_SET' => true,
    ]);
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
        "input" => [
            [
                "role" => "user",
                "content" => [
                    [
                        "type" => "input_text",
                        "text" => $prompt,
                    ],
                    // [
                    //     "type" => "input_image",
                    //     "image_url" => __DIR__ . '/../Src/1.png',
                    // ],
                    // [
                    //     "type" => "input_image",
                    //     "image_url" => __DIR__ . '/../Src/2.png',
                    // ],
                    // [
                    //     "type" => "input_image",
                    //     "image_url" => __DIR__ . '/../Src/3.png',
                    // ],
                ],
            ],
        ],
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

    echo $result;
});

Flight::start();
