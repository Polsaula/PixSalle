<?php
declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class Album{

    private int $id;
    private int $portfolioId;
    private string $title;
    private string $cover;
    private User $author;


    public function __construct(
        ?int $id,
        int $portfolioId,
        string $title,
        string $cover,
        User $author
    ){
        $this->id = $id;
        $this->portfolioId = $portfolioId;
        $this->title = $title;
        $this->cover = $cover;
        $this->author = $author;
    }


    public function id(): int{
        return $this->id;
    }

    public function portfolioId(): int{
        return $this->portfolioId;
    }

    public function title(): string{
        return $this->title;
    }

    public function cover(): string{
        return $this->cover;
    }

    public function author(): User{
        return $this->author;
    }

}
