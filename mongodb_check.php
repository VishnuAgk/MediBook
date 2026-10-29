<?php
header('Content-Type: text/plain; charset=utf-8');
if (!extension_loaded('mongodb')) {
    echo "MongoDB PHP extension: NOT ENABLED\n";
    echo "Enable/install ext-mongodb for the PHP used by XAMPP.\n";
    exit;
}
try {
    $manager = new MongoDB\Driver\Manager('mongodb://127.0.0.1:27017');
    $command = new MongoDB\Driver\Command(['ping' => 1]);
    $cursor = $manager->executeCommand('admin', $command);
    $result = $cursor->toArray()[0] ?? null;
    echo "MongoDB PHP extension: ENABLED\n";
    echo "MongoDB server: CONNECTED\n";
    echo "Database used by MediBook: medibook\n";
    echo "Ping: " . (($result && isset($result->ok) && (float)$result->ok === 1.0) ? 'OK' : 'FAILED') . "\n";
} catch (Throwable $e) {
    echo "MongoDB PHP extension: ENABLED\n";
    echo "MongoDB server: CONNECTION FAILED\n";
    echo "Reason: " . $e->getMessage() . "\n";
}
?>
