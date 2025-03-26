<?php
namespace App\Models;

use Medoo\Medoo;

class Product
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
    public function getAllProducts(): array
    {
        $products = $this->db->select('products', [
            'id',
            'brand',
            'net_weight',
            'description',
            'design'
        ], [
            'ORDER' => ['brand' => 'DESC']
        ]);

        foreach ($products as &$product) {
            $this->getProductMeasurements($product);
        }
        
        return $products;
    }

    public function getProductMeasurements(array &$product): void
    {
        $measurements = $this->db->select('results', ['value'], [
            'product_id' => $product['id']
        ]);

        $measurementValues = array_column($measurements, 'value');
        
        $product['measurement_count'] = count($measurementValues);;
        $product['max_capacity'] = $measurementValues ? max($measurementValues) : 0;
        $product['average_capacity'] = $measurementValues ? floor(array_sum($measurementValues) / count($measurementValues)) - $product['net_weight'] : 0;
        $product['safe_use'] = floor($product['average_capacity'] / 250);
    }
    
    /**
     * Get product by ID with all results
     * 
     * @param int $id Product ID
     * @return array|null Product data or null if not found
     */
    public function getProductWithMeasurements(int $id): ?array {
        $product = $this->db->get('products', [
            'id',
            'brand',
            'net_weight',
            'description',
            'design'
        ], [
            'id' => $id
        ]);
    
        if (!$product) {
            return null;
        }
    
        // Get measurement results for this product
        $results = $this->db->select('results', [
            'id',
            'value'
        ], [
            'product_id' => $id,
            'ORDER' => ['id' => 'ASC']
        ]);
    
        // Add detailed results to the product
        $product['results'] = array_map(function ($result, $index) {
            return [
                'measurement_number' => $index + 1,
                'result_value' => $result['value']
            ];
        }, $results, array_keys($results));
    
        // Add measurements array for the chart
        $product['chart_data'] = [
            'labels' => array_map(function ($index) {
                return "Measurement " . ($index + 1);
            }, array_keys($results)),
            'values' => array_column($results, 'value')
        ];

        // Add measurements to the product
        $this->getProductMeasurements($product);
        return $product;
    }
    
    /**
     * Compare multiple products
     * 
     * @param array $productIds Array of product IDs to compare
     * @return array Comparison data
     */
    public function compareProducts(array $productIds): array
    {
        $products = [];
        $comparisonData = [
            'labels' => [],
            'datasets' => []
        ];
        
        foreach ($productIds as $id) {
            $product = $this->getProductWithMeasurements($id);
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