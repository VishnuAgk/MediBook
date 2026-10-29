<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://127.0.0.1:27017");

    $client->selectDatabase('medibook')->command(['ping' => 1]);

    echo "MongoDB connection successful!";
} catch (Throwable $e) {
    echo "MongoDB connection failed: " . $e->getMessage();
}