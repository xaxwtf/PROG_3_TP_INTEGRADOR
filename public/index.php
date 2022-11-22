<?php

// Error Handling
require ( __DIR__  . "/../vendor/autoload.php");

error_reporting(-1);
ini_set('display_errors', 1);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;




// Instantiate App
$app = AppFactory::create();

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("ESTE SERIA MI TP INTEGRADOR!!!");
    return $response;
});

$app->group('/empleados', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\EmpleadoController::class . ':TraerTodos'  );
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/add]', App\Controller\EmpleadoController::class . ':cargarUno');
});
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\ProductoController::class . ':TraerTodos'  );
  $group->post('[/add]', App\Controller\ProductoController::class . ':CrearUno');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\MesaController::class . ':TraerTodos'  );
  $group->post('[/add]', App\Controller\MesaController::class . ':CrearUno');
});
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\PedidoController::class . ':TraerTodos'  );
  $group->post('[/add]', App\Controller\PedidoController::class . ':CrearUno');
});

$app->run();
