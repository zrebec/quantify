<?php
/**
 * Front controller for the Prodct Data application
 */

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use App\Twig\ChartExtension;
use Slim\Views\TwigMiddleware;
use Medoo\Medoo;
use App\Controllers\EntityController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

// Initialize Twig
$twig = Twig::create(__DIR__ . '/../templates', [
    'cache' => false,
    'debug' => true,
]);

// Initialize Slim app
$app = AppFactory::create();

// Add Twig extensions
$twig->addExtension(new ChartExtension());

// Add Twig middleware
$twig_middleware = TwigMiddleware::create($app, $twig);
$app->add($twig_middleware);

// Initialize database connection
$db = new Medoo([
    'type' => 'sqlite',
    'database' => __DIR__ . '/../data/data.sqlite'
]);

// Define routes
$app->get('/', function (Request $request, Response $response, array $args) use ($db, $twig) {
    $productController = new EntityController($db, $twig);
    return $productController->listProducts($request, $response, $args);
});

$app->get('/product/{id}', function (Request $request, Response $response, array $args) use ($db, $twig) {
    $productController = new EntityController($db, $twig);
    return $productController->showProduct($request, $response, $args);
});

$app->get('/compare', function (Request $request, Response $response, array $args) use ($db, $twig) {
    $productController = new EntityController($db, $twig);
    return $productController->compareProducts($request, $response, $args);
});

// Run the application
$app->run();