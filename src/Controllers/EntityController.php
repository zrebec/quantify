<?php
namespace App\Controllers;

use App\Models\Entity;
use Medoo\Medoo;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EntityController
{
    private Twig $view;
    private Entity $entityModel;
    
    public function __construct(Medoo $db, Twig $view)
    {
        $this->view = $view;
        $this->entityModel = new Entity($db);
    }
    
    /**
     * List all entities
     */
    public function listEntities(Response $response, array $args): Response
    {
        $entities = $this->entityModel->getAllEntities();

        try {
            return $this->view->render($response, 'entity/list.html.twig', [
                'entities' => $entities,
                'title' => 'Entity Comparison'
            ]);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            echo "Twig render exception: " . $e->getMessage();
        }
        return $response;
    }
    
    /**
     * Show a single entity with its results data
     */
    public function showEntity($response, $args) {
        $entityId = $args['id'];
        $entity = $this->entityModel->getEntityWithMeasurements($entityId);
    
        if (!$entity) {
            return $response->withStatus(404)->write('Entity not found');
        }
    
        return $this->view->render($response, 'entity/detail.html.twig', [
            'entity' => $entity
        ]);
    }
    
    /**
     * Compare multiple entities
     */
    public function compareEntities(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();
        $entityIds = isset($params['ids']) ? explode(',', $params['ids']) : [];
        
        // Make sure we have valid IDs
        $entityIds = array_filter($entityIds, function($id) {
            return is_numeric($id) && $id > 0;
        });
        
        if (empty($entityIds)) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        
        $comparisonData = $this->entityModel->compareEntities($entityIds);
        
        return $this->view->render($response, 'entity/compare.html.twig', [
            'comparison' => [
                'entities' => $comparisonData['entities'],
                'comparison_data' => $comparisonData['comparison_data']
        ]
        ]);
    }
}