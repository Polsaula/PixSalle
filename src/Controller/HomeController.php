<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\PortfolioRepository;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Views\Twig;


final class HomeController {
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig, UserRepository $userRepository) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showHome(Request $request, Response $response): Response {
        return $this->twig->render($response,
            'home.twig',
            [
            ]
        );
    }
}
