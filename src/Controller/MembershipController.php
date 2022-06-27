<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Model\Membership;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;


final class MembershipController {
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

    public function showMembership(Request $request, Response $response): Response {


        return $this->twig->render(
            $response,
            'membership.twig',
            [
                'membership' => $this->userRepository->getUserMembership($_SESSION['email'])
            ]
        );
    }

    public function updateMembership(Request $request, Response $response): Response {
        $parsedBody = $request->getParsedBody();
        if(!isset($parsedBody['membership'])){
            $response->getBody()->write("Error: Bad request. Please, refresh the page and try again");
            return $response->withStatus(400);
        }

        $desiredMembership = $request->getParsedBody()['membership'];
        if(User::validMembershipValue($desiredMembership) === false){
            $response->getBody()->write("Error: Not a valid Membership ID. Please, refresh the page and try again");
            return $response->withStatus(400);
        }

        $currentMembership = $this->userRepository->getUserMembership($_SESSION['email']);

        if($currentMembership == $desiredMembership){
            $response->getBody()->write("You already have this membership!");
            return $response->withStatus(400);
        }

        $successfulUpdate = $this->userRepository->updateUserMembership($_SESSION['email'], intval($desiredMembership));

        if($successfulUpdate){
            return $response->withStatus(200);
        }
        else{
            $response->getBody()->write("Internal error. Please, try again later");
            return $response->withStatus(500);
        }
    }

}
