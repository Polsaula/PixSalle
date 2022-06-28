<?php
declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class Blog{

    private int $id;
    private User $user;
    private string $title;
    private string $content;
    private Datetime $date;


    public function __construct(
        ?int $id,
        User $user,
        string $title,
        string $content,
        Datetime $date
    ){
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->content = $content;
        $this->date = $date;
    }


    public function id(): int{
        return $this->id;
    }

    public function title(): string{
        return $this->title;
    }

    public function content(): string{
        return $this->content;
    }

    public function user(): User{
        return $this->user;
    }
    public function date(): Datetime{
        return $this->date;
    }

}
