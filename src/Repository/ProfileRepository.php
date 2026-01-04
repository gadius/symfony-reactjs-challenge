<?php

namespace App\Repository;

use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    /**
     * Find all non-deleted profiles
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find one non-deleted profile by id
     */
    public function findOneActive(int $id): ?Profile
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(Profile $profile, bool $flush = true): void
    {
        $this->getEntityManager()->persist($profile);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Profile $profile, bool $flush = true): void
    {
        $profile->delete();
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
