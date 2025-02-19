<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    public function findDefault(): ?Theme
    {
        return $this->createQueryBuilder('t')
            ->where('t.isDefault = :isDefault')
            ->setParameter('isDefault', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByFilters(?bool $isDefault = null, ?string $searchTerm = null): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($isDefault !== null) {
            $qb->andWhere('t.isDefault = :isDefault')
               ->setParameter('isDefault', $isDefault);
        }

        if ($searchTerm) {
            $qb->andWhere('t.name LIKE :searchTerm')
               ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function unsetAllDefault(): void
    {
        $this->createQueryBuilder('t')
            ->update()
            ->set('t.isDefault', ':isDefault')
            ->setParameter('isDefault', false)
            ->getQuery()
            ->execute();
    }
}