<?php

require_once 'src/Database.php';
require_once 'src/ImportFromJSON.php';
require_once 'src/ImportFromCSV.php';

try {
    $dbPath = 'data/data.sqlite';
    $jsonPath = 'data/init_data.json';
    $productsPath = 'data/products.csv';
    $resultsPath = 'data/results.csv';

    $db = new Database($dbPath);
    $db->initialize();

    // Import from JSON
    $importJson = new ImportFromJSON($db);
    $importJson->importProducts($jsonPath);
    $importJson->importResults($jsonPath);

    // Import from CSV
    //$importCsv = new ImportFromCSV($db);
    //$importCsv->importProducts($productsPath);
    //$importCsv->importResults($resultsPath);

    echo "Import completed successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}