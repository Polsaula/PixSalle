<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\Membership;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Repository\UserRepository;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database){
        $this->databaseConnection = $database;
    }

    public function createUser(User $user): void{
        $query = <<<'QUERY'
        INSERT INTO users(email, password, createdAt, updatedAt)
        VALUES(:email, :password, :createdAt, :updatedAt)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getUserByEmail(string $email){
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getUserMembership(string $userEmail): ?int{
        $query = <<<'QUERY'
        SELECT membership FROM users WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('email', $userEmail, PDO::PARAM_STR);
        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            return intval($statement->fetch(PDO::FETCH_COLUMN));
        }
        return null;
    }

    public function updateUserMembership(string $userEmail, int $newMembership): bool{
        $query = <<<'QUERY'
        UPDATE users SET membership = :membership WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('membership', $newMembership, PDO::PARAM_INT);
        $statement->bindParam('email', $userEmail, PDO::PARAM_STR);
        return $statement->execute();
    }
}
