<?php
/**
 * Created by Artem Holovanov.
 * Date: 18.06.2025 20:08.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;

class TrackService
{
    public function __construct(
        private EntityManagerInterface $em,
        private TrackRepository $repository
    ) {}

    public function create(Track $track): Track
    {
        $this->em->persist($track);
        $this->em->flush();
        return $track;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function get(int $id): ?Track
    {
        return $this->repository->find($id);
    }
}