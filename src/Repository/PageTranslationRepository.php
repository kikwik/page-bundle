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
}