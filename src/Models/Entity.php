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
     * Get all products with their average results capacity
     * 
     * @return array List of products
     */
    public function getAllEntities(): array
    {
        $entities = $this->db->select('entities', [
            'id',
            'brand',
            'link',
            'net_weight',
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
            'entity_id' => $entity['id']
        ]);

        $measurementValues = array_column($measurements, 'value');
        
        $entity['measurement_count'] = count($measurementValues);;
        $entity['max_capacity'] = $measurementValues ? max($measurementValues) : 0;
        $entity['average_capacity'] = $measurementValues ? floor(array_sum($measurementValues) / count($measurementValues)) - $entity['net_weight'] : 0;
        $entity['safe_use'] = floor($entity['average_capacity'] / 250);
    }
    
    /**
     * Get product by ID with all results
     * 
     * @param int $id Product ID
     * @return array|null Product data or null if not found
     */
    public function getEntityWithMeasurements(int $id): ?array {
        $entity = $this->db->get('entities', [
            'id',
            'brand',
            'link',
            'net_weight',
            'description',
            'design'
        ], [
            'id' => $id
        ]);
    
        if (!$entity) {
            return null;
        }
    
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
    
    /**
     * Compare multiple products
     * 
     * @param array $entityIds Array of product IDs to compare
     * @return array Comparison data
     */
    public function compareEntities(array $entityIds): array
    {
        $products = [];
        $comparisonData = [
            'labels' => [],
            'datasets' => []
        ];
        
        foreach ($entityIds as $id) {
            $product = $this->getEntityWithMeasurements($id);
            if ($product) {
                $products[] = $product;
                
        
                // Add to comparison chart data
                if (!empty($product['chart_data']['values'])) {
                    $dataset = [
                        'label' => $product['brand'], // Use brand for the label
                        'data' => $product['chart_data']['values'], // Use chart_data values
                        'borderColor' => 'rgba(54, 162, 235, 1)', // Example color
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)', // Example color
                        'borderWidth' => 2,
                        'pointRadius' => 5
                    ];
        
                    // Ensure labels are populated
                    if (empty($comparisonData['labels']) && !empty($product['chart_data']['labels'])) {
                        $comparisonData['labels'] = $product['chart_data']['labels'];
                    }
        
                    $comparisonData['datasets'][] = $dataset;
                } else {
                    error_log("Product ID {$id} has empty chart_data['values']");
                }
            } else {
                error_log("Product ID {$id} not found or invalid.");
            }
        }
        
        return [
            'products' => $products,
            'comparison_data' => $comparisonData
        ];
    }
}