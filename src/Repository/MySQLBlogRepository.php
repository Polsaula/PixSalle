<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use DateTime;
use PDO;
use Salle\PixSalle\Model\Blog;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Repository\BlogRepository;

final class MySQLBlogRepository implements BlogRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database){
        $this->databaseConnection = $database;
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


    public function getAllEntries(){
        $query = <<<'QUERY'
        SELECT * FROM blog
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        $u = [];
    
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $user = $this->getUserById(intval($row['user_id']));
            array_push($u,  new Blog(intval($row['id']), $user, $row['title'], $row['content'], DateTime::createFromFormat("Y-m-d H:i:s", $row['date'])));
        }
        return $u;
    }


    public function getEntryById(int $entryID){
        $query = <<<'QUERY'
        SELECT * FROM blog WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $entryID, PDO::PARAM_STR);
        $statement->execute();

        if($statement->rowCount() == 1){
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $user = $this->getUserById(intval($row['user_id']));
            return new Blog(intval($row['id']), $user, $row['title'], $row['content'], DateTime::createFromFormat("Y-m-d H:i:s", $row['date']));
        }else{
            return null;
        }
    }

    public function getLatestEntry(){
        $query = <<<'QUERY'
        SELECT * FROM blog ORDER BY id DESC LIMIT 1
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();

        if($statement->rowCount() == 1){
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $user = $this->getUserById(intval($row['user_id']));
            return new Blog(intval($row['id']), $user, $row['title'], $row['content'], DateTime::createFromFormat("Y-m-d H:i:s", $row['date']));
        }else{
            return new Blog(-1, NULL, '', '', NULL);
        }
    }

    public function createBlog(int $id, string $title, string $content, DateTime $date): int{
        $query = <<<'QUERY'
        INSERT INTO blog(user_id, title, content, date)
        VALUES(:user_id, :title, :content, :date)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $cleanDate = $date->format(self::DATE_FORMAT);

        $statement->bindParam('user_id', $id, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('date', $cleanDate, PDO::PARAM_STR);

        $statement->execute();

        $blog = $this->getLatestEntry();

        return $blog->id();
        
    }


    public function updateBlog(int $id, string $title, string $content): Blog{
        $query = <<<'QUERY'
        UPDATE blog SET title = :title, content = :content WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $blog = $this->getEntryById(intval($id));

        return $blog;
        
    }

    public function deleteBlog(int $id): void{
        $query = <<<'QUERY'
        DELETE FROM blog WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        
    }

    
}
