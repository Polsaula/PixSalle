<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Repository\BlogRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;


final class BlogController {
    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;
    private BlogRepository $blogRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository,
        BlogRepository $blogRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->blogRepository = $blogRepository;
        $this->validator = new ValidatorService();
    }

    public function showBlog(Request $request, Response $response): Response {
        
        $entries = $this->blogRepository->getAllEntries();

        return $this->twig->render(
            $response,
            'blog.twig',
            [
                'entries' => $entries
            ]
        );
    }

    public function showBlogDetail(Request $request, Response $response): Response {

        $blogId = $request->getAttribute('id');
        $entry = $this->blogRepository->getEntryById(intval($blogId));

        return $this->twig->render(
            $response,
            'blogDetail.twig',
            [
                'entry' => $entry
            ]
        );
    }
}
