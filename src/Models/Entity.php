<?php
namespace App\Models;

use Medoo\Medoo;

class Entity
{
    private $db;
    
    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }
    
    /**
     * Get all entities with their average results capacity
     * 
     * @return array List of entities
     */
    public function getAllEntities(): array
    {
        $entities = $this->db->select('entities', [
            'id',
            'brand',
            'link',
            'description',
            'design'
        ], [
            'ORDER' => Medoo::raw('brand COLLATE NOCASE ASC'),
        ]);

        foreach ($entities as &$entity) {
            $this->getEntityMeasurements($entity);
        }
        
        return $entities;
    }

    public function getEntityMeasurements(array &$entity): void
    {
        $measurements = $this->db->select('measurements', ['value'], [
            'entity_id' => $entity['id'],
        ]);

        $measurementValues = array_column($measurements, 'value');

        
        $entity['properties'] = $this->getEntityProperties($entity['id']);
        $entity['measurement_count'] = count($measurementValues);
        $entity['max_capacity'] = $measurementValues ? max($measurementValues) : 0;
        $entity['average_capacity'] = $measurementValues ? floor(array_sum($measurementValues) / count($measurementValues)) - $entity['properties']['net_weight'] : 0;
        $entity['safe_use'] = floor($entity['average_capacity'] / 250);
    }
    
    /**
     * Get entity by ID with all results
     * 
     * @param int $id Entity ID
     * @return array|null Entity data or null if not found
     */
    public function getEntityWithMeasurements(int $id): ?array {
        $entity = $this->db->get('entities', [
            'id',
            'brand',
            'image',
            'link',
            'description',
            'design'
        ], [
            'id' => $id
        ]);
    
        if (!$entity) return null;
    
        // Get entity properties
        $entity['properties'] = $this->getEntityProperties($id);
    
        // Get measurement results for this entity
        $results = $this->db->select('measurements', [
            'id',
            'value',
            'saturation',
            'note'
        ], [
            'entity_id' => $id,
            'ORDER' => ['id' => 'ASC']
        ]);
    
        // Add detailed results to the entity
        $entity['results'] = array_map(function ($result, $index) {
            return [
                'measurement_number' => $index + 1,
                'result_value' => $result['value'],
                'saturation' => $result['saturation'],
                'note' => $result['note'],
            ];
        }, $results, array_keys($results));
    
        // Add measurements array for the chart
        $entity['chart_data'] = [
            'labels' => array_map(function ($index) {
                return "Measurement " . ($index + 1);
            }, array_keys($results)),
            'values' => array_column($results, 'value')
        ];
    
        // Add measurements to the entity
        $this->getEntityMeasurements($entity);
        return $entity;
    }

    private function getEntityProperties(int $entityId): array
    {
        $properties = $this->db->select('entitiy_properties', [
            'property_name',
            'property_value'
        ], [
            'entity_id' => $entityId
        ]);

        $result = [];
        foreach ($properties as $property) {
            $result[$property['property_name']] = $property['property_value'];
        }

        return $result;
    }
    
    /**
     * Compare multiple entities
     * 
     * @param array $entityIds Array of entity IDs to compare
     * @return array Comparison data
     */
    public function compareEntities(array $entityIds): array
    {
        $entities = [];
        $comparisonData = [
            'labels' => [],
            'datasets' => []
        ];
        
        foreach ($entityIds as $id) {
            $entity = $this->getEntityWithMeasurements($id);
            if ($entity) {
                $entity['properties'] = $this->getEntityProperties($id);
                $entities[] = $entity;
                
        
                // Add to comparison chart data
                if (!empty($entity['chart_data']['values'])) {
                    $dataset = [
                        'label' => $entity['brand'], // Use brand for the label
                        'data' => $entity['chart_data']['values'], // Use chart_data values
                        'borderColor' => 'rgba(54, 162, 235, 1)', // Example color
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)', // Example color
                        'borderWidth' => 2,
                        'pointRadius' => 5
                    ];
        
                    // Ensure labels are populated
                    foreach ($entity['chart_data']['labels'] as $label) {
                        /**
                         * Ensures the label is added to the graph if it is not already present.
                         * This guarantees a complete and accurate representation of data across entities.
                         */
                        if (!in_array($label, $comparisonData['labels'])) {
                            $comparisonData['labels'][] = $label;
                        }
                    }
        
                    $comparisonData['datasets'][] = $dataset;
                } else {
                    error_log("Entity ID {$id} has empty chart_data['values']");
                }
            } else {
                error_log("Entity ID {$id} not found or invalid.");
            }
        }
        
        return [
            'entities' => $entities,
            'comparison_data' => $comparisonData
        ];
    }
}