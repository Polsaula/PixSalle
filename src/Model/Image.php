<?php
declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class Image{

    private int $id;
    private int $albumId;
    private string $link;
    private User $author;


    public function __construct(
        ?int $id,
        int $albumId,
        string $link,
        User $author
    ){
        $this->id = $id;
        $this->albumId = $albumId;
        $this->link = $link;
        $this->author = $author;;
    }


    public function id(): int{
        return $this->id;
    }

    public function albumId(): int{
        return $this->albumId;
    }

    public function link(): string{
        return $this->link;
    }

    public function author(): User{
        return $this->author;
    }

}
