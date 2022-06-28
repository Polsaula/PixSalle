<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Portfolio;

interface BlogRepository {
    public function getAllEntries();
    public function getUserById(int $userID);
    public function getEntryById(int $entryID);
}
