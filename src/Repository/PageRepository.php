<?php

namespace Kikwik\PageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator;
use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;


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

    /**
     * @param PageInterface $page
     * @param $locale
     * @return PageInterface[]
     */
    public function getChildrenWithTranslations(PageInterface $page, $locale): array
    {
        return $this->getChildrenQueryBuilder($page, true)
            ->leftJoin('node.translations', 'translations')->addSelect('translations')
            ->andWhere('translations.locale = :locale')->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param PageInterface $page
     * @param string $locale
     * @return PageInterface[]
     */
    public function getPathWithTranslations(PageInterface $page, string $locale): array
    {
        return $this->getPathQueryBuilder($page)
            ->leftJoin('node.translations', 'translations')->addSelect('translations')
            ->andWhere('translations.locale = :locale')->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param PageInterface $page
     * @param string $locale
     * @return PageInterface
     */
    public function getTreeWithTranslations(PageInterface $page, string $locale): PageInterface
    {
        $this->getEntityManager()->getConfiguration()->addCustomHydrationMode('tree', TreeObjectHydrator::class);

        return $this->createQueryBuilder('node')
            ->leftJoin('node.translations', 'translations')->addSelect('translations')
            ->andWhere('translations.locale = :locale')->setParameter('locale', $locale)
            ->andWhere('node.root = :root')->setParameter('root', $page->getRoot())
            ->andWhere('node.lft >= :left')->setParameter('left', $page->getLft())
            ->andWhere('node.rgt <= :right')->setParameter('right', $page->getRgt())
            ->orderBy('node.lft', 'ASC')
            ->getQuery()
            ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getOneOrNullResult('tree');
    }
}
