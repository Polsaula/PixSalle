<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use DateTime;
use PDO;
use Salle\PixSalle\Model\Album;
use Salle\PixSalle\Model\Image;
use Salle\PixSalle\Model\Portfolio;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Repository\ImageRepository;

final class MySQLImageRepository implements ImageRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database){
        $this->databaseConnection = $database;
    }

    public function createPortfolio(int $id, string $title): void{
        $query = <<<'QUERY'
        INSERT INTO portfolio(user_id, title)
        VALUES(:user_id, :title)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('user_id', $id, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getUserPortfolio(int $id){
        $query = <<<'QUERY'
        SELECT * FROM portfolio WHERE user_id = :id 
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        if($statement->rowCount() == 1){
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return new Portfolio(intval($row['id']), intval($row['user_id']), $row['title']);
        }else{
            return new Portfolio(-1, -1, '');
        }
    }


    public function getFirstAlbumImage(int $albumID){
        $query = <<<'QUERY'
        SELECT * FROM photo WHERE album_id = :id ORDER BY id ASC LIMIT 1
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $albumID, PDO::PARAM_STR);
        $statement->execute();

        if($statement->rowCount() == 1){
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return $row['link'];
        }else{
            return '';
        }
    }

    public function getPortfolioAlbums(int $id){
        $query = <<<'QUERY'
        SELECT * FROM album WHERE portfolio_id = :id 
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        $u = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $cover = $this->getFirstAlbumImage(intval($row['id']));
            $portfolio = $this->getPortfolioById(intval($row['portfolio_id']));
            $user = $this->getUserById($portfolio->userId());

            array_push($u,  new Album(intval($row['id']), intval($row['portfolio_id']), $row['title'], $cover, $user));
        }
        return $u;
    }

    public function createAlbum(int $id, string $title): void{
        $query = <<<'QUERY'
        INSERT INTO album(portfolio_id, title)
        VALUES(:portfolio_id, :title)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('portfolio_id', $id, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getPortfolioById(int $portfolioId){
        $query = <<<'QUERY'
        SELECT * FROM portfolio WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $portfolioId, PDO::PARAM_STR);
        $statement->execute();

        if($statement->rowCount() == 1){
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return new Portfolio(intval($row['id']), intval($row['user_id']), $row['title']);
        }else{
            return new Portfolio(-1, -1, '');
        }
    }

    

    public function getUserById(int $userID){
        $query = <<<'QUERY'
        SELECT * FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $userID, PDO::PARAM_STR);
        $statement->execute();

        if($statement->rowCount() == 1){
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return new User(intval($row['id']), $row['email'], $row['password'], $row['username'], $row['phoneNumber'],  $row['wallet'], $row['profilePic'], DateTime::createFromFormat("Y-m-d H:i:s", $row['createdAt']), DateTime::createFromFormat("Y-m-d H:i:s", $row['updatedAt']));
        }else{
            return new User(-1, '', '', '', '',  '', '','', '');
        }
    }

    public function getAlbumById(int $albumID){
        $query = <<<'QUERY'
        SELECT * FROM album WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $albumID, PDO::PARAM_STR);
        $statement->execute();
        

        if($statement->rowCount() == 1){
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $cover = $this->getFirstAlbumImage(intval($row['id']));

            $portfolio = $this->getPortfolioById(intval($row['portfolio_id']));
            $user = $this->getUserById($portfolio->userId());


            return new Album(intval($row['id']), intval($row['portfolio_id']), $row['title'], $cover, $user);
        }else{
            return new Album(-1, -1, '', '');
        }
    }


    public function getAlbumImages(int $id){
        $query = <<<'QUERY'
        SELECT * FROM photo WHERE album_id = :id 
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();

        $u = [];

        $album = $this->getAlbumById(intval($id));
        $portfolio = $this->getPortfolioById($album->portfolioId());
        $user = $this->getUserById($portfolio->userId());

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($u,  new Image(intval($row['id']), intval($row['album_id']), $row['link'], $user));
        }
        return $u;
    }
    
    public function createImage(int $albumId, string $link){
        $query = <<<'QUERY'
        INSERT INTO photo(album_id, link)
        VALUES(:album_id, :link)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('album_id', $albumId, PDO::PARAM_STR);
        $statement->bindParam('link', $link, PDO::PARAM_STR);

        $statement->execute();
    }


    public function deleteImage(int $imageId){
        $query = <<<'QUERY'
        DELETE FROM photo WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $imageId, PDO::PARAM_STR);
        $statement->execute();
    }

    public function deleteAlbum(int $albumID){

        $images = $this->getAlbumImages($albumID);
        
        foreach($images as $item) {
            $this->deleteImage($item->id());
        }

        $query = <<<'QUERY'
        DELETE FROM album WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $albumID, PDO::PARAM_STR);
        $statement->execute();
    }


    public function getAllImages(){
        $query = <<<'QUERY'
        SELECT * FROM photo
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $u = [];

    
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $album = $this->getAlbumById(intval($row['album_id']));
            array_push($u,  new Image(intval($row['id']), intval($row['album_id']), $row['link'], $album->author()));
        }
        return $u;
    }
}
