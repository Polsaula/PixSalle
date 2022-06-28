<?php
declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Repository\ImageRepository;
use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;


final class AlbumController {
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

    public function showAlbum(Request $request, Response $response): Response {
        
        $albumId = $request->getAttribute('id');
        $user =  $this->userRepository->getUserByEmail($_SESSION['email']);
        $images =  $this->imageRepository->getAlbumImages(intval($albumId));
        $album =  $this->imageRepository->getAlbumById(intval($albumId));

        $hasBarcode=file_exists("assets/img/barcodes/$albumId.png");

        return $this->twig->render(
            $response,
            'albumDetail.twig',
            [
                'user' => $user,
                'images' => $images,
                'album' => $album,
                'hasBarcode' => $hasBarcode
            ]
        );
    }


    public function generateQRCode(Request $request, Response $response): Response{

        $albumId = $request->getAttribute('albumId');

        $album =  $this->imageRepository->getAlbumById(intval($albumId));

        $data = array(
            'symbology' => 'QRCode',
            'code' => 'http://localhost:8030/portfolio/album/'.$albumId,
            'dpi' => 300,
            'height' => 300,
            'width' => 300,
            'humanReadable' => $album->title()
        );

        $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => json_encode( $data ),
            'header' =>  "Content-Type: application/json\r\n" .
                        "Accept: image/png\r\n"
            )
        );

        $context  = stream_context_create( $options );
        $url = "https://barcodeprowebapi.azurewebsites.net/BarcodeGenerator";
        $resposta = file_get_contents( $url, false, $context );

        file_put_contents("assets/img/barcodes/$albumId.png", $resposta);

        return $response->withHeader('Location', '/portfolio/album/'.$albumId)->withStatus(302);
    }

    public function addImage(Request $request, Response $response): Response {
        
        $albumId = $request->getAttribute('id');
        $data = $request->getParsedBody();

        $this->imageRepository->createImage(intval($albumId), $data['imageURL']);

        return $response->withHeader('Location', '/portfolio/album/'.$albumId)->withStatus(302);
    }


    public function deleteImage(Request $request, Response $response): Response {
        
        $albumId = $request->getAttribute('id');
        $data = $request->getParsedBody();

        echo "He entrat aqui";

        if($data['imageId'] != null || !isset($data['imageId'])){
            $this->imageRepository->deleteImage(intval($albumId), intval($data['imageId']));
            return $response->withHeader('Location', '/portfolio')->withStatus(302);   
        }else{
            $this->imageRepository->deleteAlbum($albumId);
            return $response->withHeader('Location', '/portfolio')->withStatus(302);
        }

    }

}
