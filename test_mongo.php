<?php

require_once 'db.php';

echo "<h2>MongoDB Connection Test</h2>";

try {
    $doctors = mongoFind('doctors');

    echo "<p>Connection: <b>SUCCESS</b></p>";
    echo "<p>Database: <b>medibook_new</b></p>";
    echo "<p>Doctors found: <b>" . count($doctors) . "</b></p>";

    echo "<pre>";
    print_r($doctors);
    echo "</pre>";

} catch (Throwable $e) {
    echo "<p>ERROR:</p>";
    echo "<pre>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
}
?>