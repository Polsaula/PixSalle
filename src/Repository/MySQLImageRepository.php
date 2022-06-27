<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use DateTime;
use PDO;
use Salle\PixSalle\Model\Album;
use Salle\PixSalle\Model\Portfolio;
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
            array_push($u,  new Album(intval($row['id']), intval($row['portfolio_id']), $row['title'], $cover));
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



    


    

}
