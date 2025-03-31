<?php
/**
 * Import script for migration from CSV to SQLite
 * 
 * This script reads CSV data exported from Google Sheets
 * and imports it into an SQLite database
 */

require_once 'vendor/autoload.php';

class Database {
    private $pdo;
    
    public function __construct(string $dbPath) {
        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
    }
    
    public function initialize() {
        // Drop if exists and create tables from schema file
        $schema = file_get_contents('data/schema.sql');
        $this->pdo->exec($schema);
        echo "Database schema initialized.\n";
    }

    public function parseJson(string $filePath): array {
        $json = file_get_contents($filePath);
        return json_decode($json, true) ?? [];
    }
    
    
    public function importProducts(string $filePath) {
        $stmt = $this->pdo->prepare("
            INSERT INTO products 
            (brand, net_weight, design, description) 
            VALUES (?, ?, ?, ?)
        ");

        $data = $this->parseJson($filePath);
        foreach ($data as $row) {
            $stmt->execute([
                $row['brand'],
                intval($row['net weight']),
                $row['design'] ?? null,
                $row['description'] ?? null,
            ]);
        }

        echo "Products imported successfully.\n";   
    }
    
    public function importResults(string $filePath) {
        $stmt = $this->pdo->prepare("
            INSERT INTO results 
            (product_id, value, saturation, note, date) 
            VALUES ((SELECT id FROM products WHERE brand = ?), ?, ?, ?, ?)
        ");

        $data = $this->parseJson($filePath);
        foreach ($data as $row) {
            if (!isset($row['measurements'])) {
                continue; // Skip if measurements is not defined yet
            }

            foreach ($row['measurements'] as $measurement) {
                $stmt->execute([
                    $row['brand'],
                    intval($measurement['value']),
                    intval($measurement['saturation']),
                    $measurement['note'] ?? null
                ]);
            }
        }
        echo "Results imported successfully.\n";
    }
}

function parseCsv(string $filePath): array {
    $data = [];
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $rowData = [];
            foreach ($header as $i => $column) {
                $rowData[$column] = $row[$i] ?? null;
            }
            
            $data[] = $rowData;
        }
        fclose($handle);
    }
    return $data;
}

// Main execution
try {
    $dbPath = 'data/data.sqlite';
    $jsonPath = 'data/init_data.json';
    
    $db = new Database($dbPath);
    $db->initialize();
    
    $db->importProducts($jsonPath);
    $db->importResults($jsonPath);
    
    echo "Import completed successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}