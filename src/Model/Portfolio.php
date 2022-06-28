<?php
declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class Portfolio{

    private int $id;
    private int $userId;
    private string $title;


    public function __construct(
        ?int $id,
        int $userId,
        string $title
    ){
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
    }


    public function id(): int{
        return $this->id;
    }

    public function userId(): int{
        return $this->userId;
    }

    public function title(): string{
        return $this->title;
    }

}