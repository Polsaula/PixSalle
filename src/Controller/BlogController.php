<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use DateTime;
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

        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        return $this->twig->render(
            $response,
            'blog.twig',
            [
                'entries' => $entries,
                'userId' => $user->id()
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


    public function getBlog(Request $request, Response $response): Response {
        $entries = $this->blogRepository->getAllEntries();

        $responseBody = json_encode($entries);

        $response->getBody()->write($responseBody);
        return $response->withHeader('content-type', 'application/json')->withStatus(200);
    }

    public function getBlogId(Request $request, Response $response): Response {
        $blogId = $request->getAttribute('id');

        $entry = $this->blogRepository->getEntryById(intval($blogId));
        if($entry != null){
            $responseBody = json_encode($entry);
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(200);
        }
        
        $resposta = <<<body
        {"message": "Blog entry with id $blogId does not exist"}
        body;
        $response->getBody()->write($resposta);
        return $response->withHeader('content-type', 'application/json')->withStatus(404);

    }

    public function postBlog(Request $request, Response $response): Response {
        
        $parsedBody = $request->getParsedBody();
        if(isset($parsedBody['content']) && isset($parsedBody['title']) && isset($parsedBody['userId'])){

            $resposta = $this->blogRepository->createBlog(intval($parsedBody['userId']), $parsedBody['title'], $parsedBody['content'], new DateTime());
            
            $response->getBody()->write(json_encode($resposta));
            return $response->withHeader('content-type', 'application/json')->withStatus(201);
        }

        $resposta = <<<body
        {"message": "'title' and/or 'content' and/or 'userId' are missing"}
        body;
        $response->getBody()->write($resposta);
        return $response->withHeader('content-type', 'application/json')->withStatus(400);
    }

    public function putBlog(Request $request, Response $response): Response {
        $blogId = $request->getAttribute('id');
        $parsedBody = $request->getParsedBody();


        if( isset($parsedBody['content']) && isset($parsedBody['title']) ){
            
            $blog = $this->blogRepository->getEntryById(intval($blogId));

            if($blog != null){
                $resposta = $this->blogRepository->updateBlog(intval($blogId), $parsedBody['title'], $parsedBody['content']);
                
                $response->getBody()->write(json_encode($resposta));
                return $response->withHeader('content-type', 'application/json')->withStatus(200);
            }

            $resposta = <<<body
            {"message": "Blog entry with id $blogId does not exist"}
            body;
            $response->getBody()->write($resposta);
            return $response->withHeader('content-type', 'application/json')->withStatus(404);
        }

        $resposta = <<<body
        {"message": "The title and/or content cannot be empty"}
        body;
        $response->getBody()->write($resposta);
        return $response->withHeader('content-type', 'application/json')->withStatus(400);
    }

    
    public function deleteBlogId(Request $request, Response $response): Response {
        $blogId = $request->getAttribute('id');

        $blog = $this->blogRepository->getEntryById(intval($blogId));
        if($blog != null){
            
            $this->blogRepository->deleteBlog(intval($blogId));
            $responseBody = <<<body
            {"message": "Blog entry with id $blogId was successfully deleted"}
            body;
            $response->getBody()->write($responseBody);
            return $response->withHeader('content-type', 'application/json')->withStatus(200);
        }

        $resposta = <<<body
        {"message": "Blog entry with id $blogId does not exist"}
        body;
        $response->getBody()->write($resposta);
        return $response->withHeader('content-type', 'application/json')->withStatus(200);
    }


}
