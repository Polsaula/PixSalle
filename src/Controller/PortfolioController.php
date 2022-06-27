<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Repository\ImageRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Flash\Messages;
use Slim\Views\Twig;


final class PortfolioController {
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;
    private Messages $flash;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository,
        ImageRepository $imageRepository,
        Messages $flash
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
        $this->validator = new ValidatorService();
        $this->flash = $flash;
    }

    public function showPortfolio(Request $request, Response $response): Response {
        $membership = $this->userRepository->getUserMembership($_SESSION['email']);
        
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        $portfolio = $this->imageRepository->getUserPortfolio($user->id());

        if($portfolio->id() == -1){
            return $this->twig->render(
                $response,
                'portfolio.twig',
                [
                    'membership' => $membership,
                    'buit' => true
                ]
            );
        }else{
            $albums = $this->imageRepository->getPortfolioAlbums($portfolio->id());

            return $this->twig->render(
                $response,
                'portfolio.twig',
                [
                    'membership' => $membership,
                    'albums' => $albums
                ]
            );
        }
    }

    public function createPortfolio(Request $request, Response $response): Response {

        $data = $request->getParsedBody();

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);
        $this->imageRepository->createPortfolio($user->id(), $data['title']);

        return $response->withHeader('Location', '/portfolio')->withStatus(302);
    }

    public function createAlbum(Request $request, Response $response): Response {

        $data = $request->getParsedBody();
       
        if(strlen($data['album-name']) > 0){

            $user = $this->userRepository->getUserByEmail($_SESSION['email']);

            $currentWallet = floatval($_SESSION['wallet']);
            if($currentWallet < 2){
                $this->flash->addMessage('error', 'Insuficient funds to create new Album');
            }else{
                $_SESSION['wallet'] = $currentWallet-2;;
                $this->userRepository->getUserByEmail($_SESSION['email'], strval($_SESSION['wallet']));
                $portfolio = $this->imageRepository->getUserPortfolio($user->id());
                $this->imageRepository->createAlbum($portfolio->id(), $data['album-name']);
            }

        }else{
            $this->flash->addMessage('error', 'Album title cannot be empty');
        }

        return $response->withHeader('Location', '/portfolio')->withStatus(302);
    }


}
