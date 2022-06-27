<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;


final class ExploreController {
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository,
        ImageRepository $imageRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
        $this->validator = new ValidatorService();
    }

    public function showExplore(Request $request, Response $response): Response {
        $membership = $this->userRepository->getUserMembership($_SESSION['email']);

        return $this->twig->render(
            $response,
            'explore.twig',
            [
            ]
        );
    }
}
