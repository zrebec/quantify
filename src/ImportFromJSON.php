<?php
namespace App;

use Exception;

require_once 'Database.php';

class ImportFromJSON {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getPDO();
    }

    public function importEntities(string $filePath) {
        $stmt = $this->pdo->prepare("
            INSERT INTO entities 
            (brand, link, net_weight, design, description) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $data = $this->parseJson($filePath);
        foreach ($data as $row) {
            $stmt->execute([
                $row['brand'],
                $row['link'] ?? null,
                intval($row['net weight']),
                $row['design'] ?? null,
                $row['description'] ?? null,
            ]);
        }
        echo "Entities imported successfully.\n";
    }

    public function importMeasurements(string $filePath) {
        $stmt = $this->pdo->prepare("
            INSERT INTO measurements 
            (entity_id, date, value, saturation, note) 
            VALUES ((SELECT id FROM entities WHERE brand = ?), ?, ?, ?, ?)
        ");

        $data = $this->parseJson($filePath);
        foreach ($data as $row) {
            if (!isset($row['measurements'])) {
                continue;
            }

            foreach ($row['measurements'] as $measurement) {
                $stmt->execute([
                    $row['brand'],
                    $measurement['date'] ?? '1970-01-01',
                    intval($measurement['value']),
                    intval($measurement['saturation']),
                    $measurement['note'] ?? null,
                ]);
            }
        }
        echo "Measurements imported successfully.\n";
    }

    private function parseJson(string $filePath): array {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        }

        $json = file_get_contents($filePath);
        return json_decode($json, true) ?? [];
    }
}