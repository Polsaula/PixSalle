<?php
declare(strict_types=1);

namespace Salle\PixSalle\Middleware;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class MainMiddleware {
    public function __invoke(Request $request, RequestHandler $next): ?Response
    {
        $response = $next->handle($request);

        if (isset($_SESSION['email'])) {
            header("Location:/");
        }
        $response = $next->handle($request);
        return $response;
    }
}