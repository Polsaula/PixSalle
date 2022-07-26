<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;


final class WalletController {
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->validator = new ValidatorService();
    }

    public function showWallet(Request $request, Response $response): Response {
        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'wallet' => $_SESSION['wallet']
            ]
        );
    }

    public function updateWallet(Request $request, Response $response): Response{

        $data = $request->getParsedBody();
        $_SESSION['wallet'] = floatval($data['resultat']);

        $this->userRepository->updateWallet($_SESSION['email'], $data['resultat']);

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'wallet' => $_SESSION['wallet']
            ]
        );

    }
}
