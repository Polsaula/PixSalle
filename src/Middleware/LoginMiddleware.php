<?php
declare(strict_types=1);

namespace Salle\PixSalle\Middleware;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class LoginMiddleware {
    public function __invoke(Request $request, RequestHandler $next): ?Response
    {
        
        if (!isset($_SESSION['email'])) {
            header("Location:/sign-in");
        }
        $response = $next->handle($request);
        return $response;
    }
}