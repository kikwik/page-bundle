<?php

namespace Kikwik\PageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Kikwik\PageBundle\Model\PageInterface;

/**
 * @extends ServiceEntityRepository<PageInterface>
 */
class PageRepository extends NestedTreeRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry->getManager(), $registry->getManager()->getClassMetadata(PageInterface::class));
    }

}
