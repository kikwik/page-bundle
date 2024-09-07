<?php

namespace Kikwik\PageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Kikwik\PageBundle\Model\PageInterface;


class PageRepository extends NestedTreeRepository implements ServiceEntityRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private string $entityClass,
    )
    {
        parent::__construct(
            $registry->getManager(),
            $registry->getManager()->getClassMetadata($this->entityClass)
        );
    }


}
