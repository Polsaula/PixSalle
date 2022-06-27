<?php
declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class Image{

    private int $id;
    private int $albumId;
    private string $link;


    public function __construct(
        ?int $id,
        string $albumId,
        string $link
    ){
        $this->id = $id;
        $this->albumId = $albumId;
        $this->link = $link;
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

}
