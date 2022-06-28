<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Portfolio;

interface ImageRepository {
    public function createPortfolio(int $id, string $title): void;
    public function getUserPortfolio(int $id);
    public function getFirstAlbumImage(int $albumID);
    public function createAlbum(int $id, string $title): void;
    public function getPortfolioById(int $portfolioId);
    public function getAlbumById(int $albumID);
    public function getUserById(int $userID);
    public function getAlbumImages(int $id);
    public function createImage(int $albumId, string $link);
    public function deleteImage(int $imageId);
    public function deleteAlbum(int $albumID);
    public function getAllImages();
}
