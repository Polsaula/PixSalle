<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Ramsey\Uuid\Uuid;
use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;


final class ProfileController {
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;
    private Messages $flash;


    public function __construct(
        Twig $twig,
        UserRepository $userRepository,
        Messages $flash
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->flash = $flash;
        $this->flash->__construct($_SESSION);
        $this->validator = new ValidatorService();
    }

    public function showProfile(Request $request, Response $response): Response {

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'username' => $user->username(),
                'phoneNumber' => $user->phoneNumber()
            ]
        );
    }

    private function updateProfilePicture(&$errors){
        $currentDirectory = getcwd();
        $uploadDirectory = "/uploads/";

        $fileExtensionsAllowed = ['jpg', 'png']; // These will be the only file extensions allowed
        $fileName = $_FILES['newProfilePic']['tmp_name'];
        $fileSize = $_FILES['newProfilePic']['size'];
        $info = getimagesize($fileName);
        $fileTmpName = $_FILES['newProfilePic']['tmp_name'];
        $ext = pathinfo($_FILES['newProfilePic']['name'], PATHINFO_EXTENSION);

        $error = false;

        $myuuid = Uuid::uuid4();    //Generate random UUID
        $uploadPath = $currentDirectory . $uploadDirectory . $myuuid;

        if (!in_array($ext, $fileExtensionsAllowed)) {
            $this->flash->addMessage('error', 'This file extension is not allowed. Please upload a JPG or PNG file');
            $error = true;
        }

        if ($fileSize > 1000000) {
            $this->flash->addMessage('error', 'File exceeds maximum size (1MB)');
            $error = true;
        }

        if ($info[0] > 500 || $info[1] > 500) {
            $this->flash->addMessage('error', 'Image dimensions must be less or equal to 500x500');
            $error = true;
        }


        if( $error == false ){
            move_uploaded_file($fileTmpName, $uploadPath);
            $this->userRepository->updateUserProfilePicture($_SESSION['email'], $myuuid->toString());
            return $myuuid->toString();
        }else{
            return "";
        }

    }

    private function validatePhoneNumber(string $phoneNumber) {
        if ((strlen($phoneNumber) != 9) || !is_numeric($phoneNumber) || !str_starts_with($phoneNumber, '6')) {
            return false;
        } else {
            return true;
        }
    }

    public function updateProfileData(Request $request, Response $response): Response
    {
        $errors = [];

        if (isset($_POST['submit'])) {
            if ($_FILES['newProfilePic']['size'] != 0){
                $fileName = $this->updateProfilePicture($errors);
                if(strlen($fileName) > 0){
                    $imageDir = $fileName;
                    $_SESSION['picture'] = $fileName;
                }
            } else {
                $imageDir = $this->userRepository->getPicByEmail($_SESSION['email']);
            }

            if (isset($_POST['username'])) {
                $this->userRepository->updateUsername($_SESSION['email'], $_POST['username']);
                $username =  $_POST['username'];
            } else {
                $user = $this->userRepository->getUserByEmail($_SESSION['email']);
                $username = $user->username();
                $_SESSION['username'] = $user->username();
            }

            if (!empty($_POST['phoneNumber'])) {
                if ($this->validatePhoneNumber($_POST['phoneNumber'])) {
                    $this->userRepository->updatePhoneNumber($_SESSION['email'], $_POST['phoneNumber']);
                    $phoneNumber =  $_POST['phoneNumber'];
                } else{
                    $this->flash->addMessage('error', 'The phone number must follow the Spanish numbering plan (6XXXXXXXX)');
                    $user = $this->userRepository->getUserByEmail($_SESSION['email']);
                    $phoneNumber = $user->phoneNumber();
                }
            } else {
                $this->userRepository->updatePhoneNumber($_SESSION['email'], "");
                $phoneNumber =  "";
            }


            return $this->twig->render(
                $response,
                'profile.twig',
                [
                    'phoneNumber' => $phoneNumber,
                ]);
        } else {
            return $this->twig->render(
                $response,
                'profile.twig');
        }
    }
}
