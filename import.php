<?php

require_once 'src/Database.php';
require_once 'src/ImportFromJSON.php';

try {
    $dbPath = 'data/data.sqlite';
    //$jsonPath = 'data/init_data.json';
    $jsonPath = 'data/init.json';

    $db = new App\Database($dbPath);
    $db->initialize();

    // Import from JSON
    $importJson = new App\ImportFromJSON($db);
    $importJson->importEntities($jsonPath);
    $importJson->importEntityProperties($jsonPath);
    $importJson->importMeasurements($jsonPath);

    echo "Import completed successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}