<?php
namespace App\Models;

use Medoo\Medoo;

class AbsorptionResult
{
    private $db;
    
    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }
    
    /**
     * Get all results for a specific product
     * 
     * @param int $productId Product ID
     * @return array Results for the product
     */
    public function getResultsForProduct(int $productId): array
    {
        return $this->db->select('absorption_results', [
            'id',
            'measurement_number',
            'absorption_value'
        ], [
            'product_id' => $productId,
            'ORDER' => ['measurement_number' => 'ASC']
        ]);
    }
    
    /**
     * Get average absorption across all products
     * 
     * @return array Average absorption by level
     */
    public function getAverageAbsorptionByLevel(): array
    {
        $query = $this->db->query("
            SELECT measurement_number, AVG(absorption_value) as average_value
            FROM absorption_results
            GROUP BY measurement_number
            ORDER BY measurement_number ASC
        ");
        
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Find the highest absorption product for each level
     * 
     * @return array Highest performing product for each level
     */
    public function getTopPerformersByLevel(): array
    {
        $query = $this->db->query("
            SELECT ar.measurement_number, 
                   p.code as product_code,
                   p.id as product_id,
                   MAX(ar.absorption_value) as max_value
            FROM absorption_results ar
            JOIN products p ON ar.product_id = p.id
            GROUP BY ar.measurement_number
            ORDER BY ar.measurement_number ASC
        ");
        
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
}