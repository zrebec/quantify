<?php
require_once 'Database.php';

class ImportFromJSON {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getPDO();
    }

    public function importProducts(string $filePath) {
        $stmt = $this->pdo->prepare("
            INSERT INTO products 
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
        echo "Products imported successfully from JSON.\n";
    }

    public function importResults(string $filePath) {
        $stmt = $this->pdo->prepare("
            INSERT INTO results 
            (product_id, date, value, saturation, note) 
            VALUES ((SELECT id FROM products WHERE brand = ?), ?, ?, ?, ?)
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
        echo "Results imported successfully from JSON.\n";
    }

    private function parseJson(string $filePath): array {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        }

        $json = file_get_contents($filePath);
        return json_decode($json, true) ?? [];
    }
}