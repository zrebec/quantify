<?php
require_once 'Database.php';

class ImportFromCSV {
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

        $data = $this->parseCsv($filePath);
        foreach ($data as $row) {
            $stmt->execute([
                $row['brand'],
                $row['link'] ?? null,
                intval($row['net weight']),
                $row['design'] ?? null,
                $row['description'] ?? null,
            ]);
        }
        echo "Products imported successfully from CSV.\n";
    }

    public function importResults(string $filePath) {
        $stmt = $this->pdo->prepare("
            INSERT INTO results 
            (product_id, date, value, saturation, note) 
            VALUES ((SELECT id FROM products WHERE brand = ?), ?, ?, ?, ?)
        ");

        $data = $this->parseCsv($filePath);
        foreach ($data as $row) {
            $stmt->execute([
                $row['brand'],
                $row['date'] ?? '1970-01-01',
                intval($row['value']),
                intval($row['saturation']),
                $row['note'] ?? null,
            ]);
        }
        echo "Results imported successfully from CSV.\n";
    }

    private function parseCsv(string $filePath): array {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        }

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
}