<?php

namespace Kikwik\PageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kikwik\PageBundle\Model\PageTranslationInterface;

class PageTranslationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private string $entityClass,
    )
    {
        parent::__construct($registry, $this->entityClass);
    }


    public function findOneBySlugJoinPage(string $slug): ?PageTranslationInterface
    {
        return $this->createQueryBuilder('pt')
            ->where('pt.slug = :slug')->setParameter('slug', $slug)
            ->leftJoin('pt.page', 'p')->addSelect('p')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}