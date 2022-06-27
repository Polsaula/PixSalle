<?php
declare(strict_types=1);

//use Salle\PixSalle\ErrorHandler\HttpErrorHandler;
use Salle\PixSalle\Middleware\LoginMiddleware;
use Salle\PixSalle\Middleware\StartSessionMiddleware;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

require_once __DIR__ . '/../config/dependencies.php';

AppFactory::setContainer($container);

$app = AppFactory::create();


$app->add(TwigMiddleware::createFromContainer($app));

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);


if(!isset($_SESSION['start'])){
    session_start();
    $_SESSION['start'] = "started";
}


// $errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (
//     ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors,bool $logErrorDetails) {
//     $response = new \Slim\Psr7\Response();
//     if (isset($_SESSION['email'])) {
//         return $response->withHeader('Location', '/')->withStatus(302);
//     }else{
//         return $response->withHeader('Location', '/sign-up')->withStatus(302);
//     }
// });

$app->addBodyParsingMiddleware();


/**
 * Add Routing Middleware
 * https://www.slimframework.com/docs/v4/middleware/routing.html
 */

require_once __DIR__ . '/../config/routing.php';

$app->run();