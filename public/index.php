<?php

// Error Handling
require ( __DIR__  . "/../vendor/autoload.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

use App\Midlewares\EstaLogeado;
use App\Midlewares\EsAdmin_Socio;
use App\Midlewares\EsAdmin_Socio_Empleado_NoMozo;
use App\Midlewares\EsAdmin_Socio_Mozo;

// Instantiate App
$app = AppFactory::create();

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("ESTE SERIA MI TP INTEGRADOR!!!");
    return $response;
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\UsuarioController::class . ':TraerTodos'  )->add(new EsAdmin_Socio)->add(new EstaLogeado);  // socios y admin
  $group->get('/CSV',App\Controller\UsuarioController::class . ':Descargar'  )->add(new EsAdmin_Socio)->add(new EstaLogeado);  // socios y admin

  $group->get('/{usuario}', App\Controller\UsuarioController::class . ':TraerUno')->add(new EsAdmin_Socio)->add(new EstaLogeado);//socios y addmin
  $group->post('/perfil', App\Controller\UsuarioController::class . ':Perfil')->add(new EstaLogeado);///solo si hay alguien logeado
  $group->post('[/add]' , App\Controller\UsuarioController::class . ':cargarUno')->add(new EsAdmin_Socio)->add(new EstaLogeado);//socios y admin
  $group->post('/login', App\Controller\UsuarioController::class . ':LogearUsuario'); //todos
});

$app->group('/pendientes', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\UsuarioController::class . ':MostrarListaPendientes');//solo si hay un usuario logeado y no es mozo
  $group->post('/tomar', App\Controller\UsuarioController::class .':TomarUnPendiente');//solo si hay un usuario logeado y no es mozo
  $group->post('/notificar', App\Controller\UsuarioController::class .':FinalizarUnPendiente');//solo si hay un usuario logeado y no es mozo
})->add(new EsAdmin_Socio_Empleado_NoMozo)->add(new EstaLogeado);


$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\ProductoController::class . ':TraerTodos' )->add(new EsAdmin_Socio_Mozo);//socios, admin y mozo
  $group->get('/CSV', App\Controller\ProductoController::class . ':Descargar' )->add(new EsAdmin_Socio_Mozo);
  $group->post('[/add]', App\Controller\ProductoController::class . ':CrearUno')->add(new EsAdmin_Socio); //socios y admin  
  $group->get('/{id}', App\Controller\ProductoController::class . ':TraerUno' )->add(new EsAdmin_Socio_Mozo); // socios, admin y mozo
})->add(new EstaLogeado);

$app->group('/mesas', function (RouteCollectorProxy $group){
  $group->get('[/]', App\Controller\MesaController::class . ':TraerTodos')->add(new EsAdmin_Socio_Mozo);//socios, admin y mozo
  $group->post('[/add]', App\Controller\MesaController::class . ':CrearUno')->add(new EsAdmin_Socio); //socios, admin 
  $group->post('/cerrar', App\Controller\MesaController::class . ':CerrarUnaMesa')->add( new EsAdmin_Socio); //socios, admin 
})->add(new EstaLogeado);


$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\PedidoController::class . ':TraerTodos'  )->add(new EsAdmin_Socio);//socios, admin 
  $group->post('[/add]', App\Controller\PedidoController::class . ':CrearUno')->add(new EsAdmin_Socio_Mozo); // socios, admin , mozo
  $group->get('/{id}', App\Controller\PedidoController::class . ':TraerUno' )->add(new EsAdmin_Socio_Mozo); //socios, admin, mozo  
  $group->post('/entregar', App\Controller\PedidoController::class . ':EntregarPedido')->add(new EsAdmin_Socio_Mozo); //socios, admin , mozo
  $group->post('/cobrar', App\Controller\PedidoController::class . ':CobrarPedido')->add(new EsAdmin_Socio_Mozo); // socios, admin 
  $group->post('/demora', App\Controller\PedidoController::class . ':InformarDemora')->add(new EsAdmin_Socio_Empleado_NoMozo);
  $group->post('/listos', App\Controller\PedidoController::class . ':ListarPedidosListos')->add(new EsAdmin_Socio_Mozo);
  $group->post('/entregadosConDemora', App\Controller\PedidoController::class . ':ListarPedidosEntregadorConDemora')->add(new EsAdmin_Socio);
  
})->add(new EstaLogeado);

$app->group('/clientes', function (RouteCollectorProxy $group) {
  $group->post('/solicitarCuenta', App\Controller\ClienteController::class .':SolicitarTotal');
  $group->post('/test', App\Controller\MesaController::class . ':Testeos'); 
  $group->post('/encuesta', App\Controller\ClienteController::class . ':Encuesta');
  $group->post('/consultarDemora', App\Controller\PedidoController::class . ':ConsultarDemora');
});

$app->group('/informes', function (RouteCollectorProxy $group) {
  $group->post('/mejoresencuestas', App\Controller\InformeController::class .':MejoresEncuestas')->add(new EstaLogeado)->add(new EsAdmin_Socio);
  $group->post('/mesamasusada', App\Controller\InformeController::class .':MesaMasUsada')->add(new EstaLogeado)->add(new EsAdmin_Socio); 
  $group->post('/pedidoscondemora', App\Controller\InformeController::class . ':PedidosConDemora')->add(new EstaLogeado)->add(new EsAdmin_Socio);
  $group->post('/accionesxusuario', App\Controller\InformeController::class . ':CantidadDeAccionesxUsuario')->add(new EstaLogeado)->add(new EsAdmin_Socio);
  $group->post('/unidadesvendidasxproducto', App\Controller\InformeController::class . ':UnidadesVendidasxProducto')->add(new EstaLogeado)->add(new EsAdmin_Socio);
  $group->post('/listadelogeos', App\Controller\InformeController::class . ':ListadeLogeos')->add(new EstaLogeado)->add(new EsAdmin_Socio);
  $group->get('[/logo]', App\Controller\InformeController::class . ':GenerarLogo'); 
});


$app->run();
