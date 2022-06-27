<?php
declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class Album{

    private int $id;
    private int $portfolioId;
    private string $title;
    private string $cover;


    public function __construct(
        ?int $id,
        int $portfolioId,
        string $title,
        string $cover
    ){
        $this->id = $id;
        $this->userId = $portfolioId;
        $this->title = $title;
        $this->cover = $cover;
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

}
