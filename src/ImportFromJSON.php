<?php
namespace App;

use Exception;

require_once 'Database.php';

class ImportFromJSON {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getPDO();
    }

    public function importEntities($jsonPath) {
        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data['entities'] as $entity) {
            $stmt = $this->db->prepare('
                INSERT INTO entities (name, link, image, description, design, reusability)
                VALUES (:name, :link, :image, :description, :design, :reusability)
            ');

            $stmt->execute([
                ':name' => $entity['name'],
                ':link' => $entity['link'],
                ':image' => $entity['image'] ?? null,
                ':description' => $entity['description'] ?? null,
                ':design' => $entity['design'] ?? null,
                ':reusability' => $entity['reusage'] ?? 0
            ]);
        }
    }

    public function importEntityProperties($jsonPath) {
        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data['entities'] as $entity) {
            $entityId = $this->getEntityIdByName($entity['name']);

            foreach ($entity['properties'] as $propertyName => $propertyValue) {
                $stmt = $this->db->prepare('
                    INSERT INTO entitiy_properties (entity_id, property_name, property_value)
                    VALUES (:entity_id, :property_name, :property_value)
                ');

                $stmt->execute([
                    ':entity_id' => $entityId,
                    ':property_name' => $propertyName,
                    ':property_value' => (string)$propertyValue
                ]);
            }
        }
    }

    public function importMeasurements($jsonPath) {
        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data['entities'] as $entity) {
            $entityId = $this->getEntityIdByName($entity['name']);

            foreach ($entity['measurements'] as $measurement) {
                $stmt = $this->db->prepare('
                    INSERT INTO measurements (entity_id, value, level, note, date)
                    VALUES (:entity_id, :value, :level, :note, :date)
                ');

                $stmt->execute([
                    ':entity_id' => $entityId,
                    ':value' => $measurement['value'],
                    ':level' => $measurement['level'],
                    ':note' => $measurement['note'] ?? null,
                    ':date' => $measurement['date'] ?? '1970-01-01'
                ]);
            }
        }
    }

    private function getEntityIdByName($name) {
        $stmt = $this->db->prepare('SELECT id FROM entities WHERE name = :name');
        $stmt->execute([':name' => $name]);
        return $stmt->fetchColumn();
    }

    private function parseJson(string $filePath): array {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        }

        $json = file_get_contents($filePath);
        return json_decode($json, true) ?? [];
    }
}