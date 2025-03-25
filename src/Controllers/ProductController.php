<?php
namespace App\Controllers;

use App\Models\Product;
use Medoo\Medoo;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProductController
{
    private $db;
    private $view;
    private $productModel;
    
    public function __construct(Medoo $db, Twig $view)
    {
        $this->db = $db;
        $this->view = $view;
        $this->productModel = new Product($db);
    }
    
    /**
     * List all products
     */
    public function listProducts(Request $request, Response $response, array $args): Response
    {
        $products = $this->productModel->getAllProducts();
        
        return $this->view->render($response, 'product/list.html.twig', [
            'products' => $products,
            'title' => 'Product Comparison'
        ]);
    }
    
    /**
     * Show a single product with its absorption data
     */
    public function showProduct($request, $response, $args) {
        $productId = $args['id'];
        $product = $this->productModel->getProductWithMeasurements($productId);
    
        if (!$product) {
            return $response->withStatus(404)->write('Product not found');
        }
    
        return $this->view->render($response, 'product/detail.html.twig', [
            'product' => $product
        ]);
    }
    
    /**
     * Compare multiple products
     */
    public function compareProducts(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();
        $productIds = isset($params['ids']) ? explode(',', $params['ids']) : [];
        
        // Make sure we have valid IDs
        $productIds = array_filter($productIds, function($id) {
            return is_numeric($id) && $id > 0;
        });
        
        if (empty($productIds)) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        
        $comparisonData = $this->productModel->compareProducts($productIds);
        
        return $this->view->render($response, 'product/compare.html.twig', [
            'comparison' => [
                'products' => $comparisonData['products'],
                'comparison_data' => $comparisonData['comparison_data']
        ]
        ]);
    }
}