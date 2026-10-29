<?php

if (!extension_loaded('mongodb')) {
    die('MongoDB PHP extension is not enabled.');
}

require_once __DIR__ . '/vendor/autoload.php';

try {
    $mongo = new MongoDB\Driver\Manager('mongodb://127.0.0.1:27017');
} catch (Throwable $e) {
    die('MongoDB connection failed: ' . htmlspecialchars($e->getMessage()));
}

$dbName = 'medibook_new';

function mongoCollection(string $name): string {
    global $dbName;
    return $dbName . '.' . $name;
}

function mongoFind(string $collection, array $filter = [], array $options = []): array {
    global $mongo;

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $mongo->executeQuery(mongoCollection($collection), $query);

    $rows = [];

    foreach ($cursor as $doc) {
        $rows[] = json_decode(json_encode($doc), true);
    }

    return $rows;
}

function mongoFindOne(string $collection, array $filter = [], array $options = []): ?array {
    $options['limit'] = 1;

    $rows = mongoFind($collection, $filter, $options);

    return $rows[0] ?? null;
}

function mongoInsert(string $collection, array $document): string {
    global $mongo;

    if (!isset($document['_id'])) {
        $document['_id'] = bin2hex(random_bytes(12));
    }

    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->insert($document);

    $mongo->executeBulkWrite(
        mongoCollection($collection),
        $bulk
    );

    return (string) $document['_id'];
}

function mongoUpdate(
    string $collection,
    array $filter,
    array $update,
    array $options = []
): void {
    global $mongo;

    $bulk = new MongoDB\Driver\BulkWrite();

    $bulk->update(
        $filter,
        $update,
        $options
    );

    $mongo->executeBulkWrite(
        mongoCollection($collection),
        $bulk
    );
}

function mongoDelete(string $collection, array $filter): void {
    global $mongo;

    $bulk = new MongoDB\Driver\BulkWrite();

    $bulk->delete(
        $filter,
        ['limit' => 1]
    );

    $mongo->executeBulkWrite(
        mongoCollection($collection),
        $bulk
    );
}