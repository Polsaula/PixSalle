<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;


final class ProfileController {
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

    public function showProfile(Request $request, Response $response): Response {
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'email' => $_SESSION['email'],
                'id' => $_SESSION['user_id']
            ]
        );
    }

    public function updateProfileData(Request $request, Response $response): Response
    {
        $currentDirectory = getcwd();
        $uploadDirectory = "/uploads/";

        $errors = []; // Store errors here

        $fileExtensionsAllowed = ['jpg','png']; // These will be the only file extensions allowed
        $fileName = $_FILES['newProfilePic']['tmp_name'];
        $fileSize = $_FILES['newProfilePic']['size'];
        $info = getimagesize($fileName);
        $fileTmpName  = $_FILES['newProfilePic']['tmp_name'];
        $ext = pathinfo($_FILES['newProfilePic']['name'], PATHINFO_EXTENSION);

        $uploadPath = $currentDirectory . $uploadDirectory . basename($fileName);

        if (! in_array($ext,$fileExtensionsAllowed)) {
            $errors['ext'] = "This file extension is not allowed. Please upload a JPG or PNG file";
        }

        if ($fileSize > 1000000) {
            $errors['size'] = "File exceeds maximum size (1MB)";
        }

        if ($info[0] != 500 || $info[1] != 500) {
            $errors['res'] = $info[0] . "x" . $info[1];
        }

        if (empty($errors)) {
            $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        } else {
            foreach ($errors as $error) {
                echo $error . "These are the errors" . "\n";
            }
        }

        $imageDir = "uploads/" . basename($fileName);
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'email' => $_SESSION['email'],
                'profilePic' => $imageDir,
                'errors' => $errors
            ]);
    }
}
