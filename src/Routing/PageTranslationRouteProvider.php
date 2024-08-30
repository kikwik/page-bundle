<?php

namespace Kikwik\PageBundle\Routing;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Kikwik\PageBundle\Entity\Page;
use Kikwik\PageBundle\Entity\PageTranslation;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

class PageTranslationRouteProvider implements RouteProviderInterface
{
    private EntityManagerInterface $entityManager;
    public function __construct(Registry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    public function getRouteCollectionForRequest(Request $request): RouteCollection
    {
        $collection = new RouteCollection();

        // Assuming slug is extracted from the request
        $slug = $request->getPathInfo();
        $slug = ltrim($slug, '/');

        $pageTranslation = $this->entityManager->getRepository(PageTranslation::class)->findOneBy(['slug' => $slug]);

        if ($pageTranslation) {
            $route = new Route($pageTranslation->getSlug(), [
                '_controller' => 'kikwik_page.controller.page_controller::show',
                'pageTranslation' => $pageTranslation
            ]);

            $collection->add($pageTranslation->getPage()->getRouteName() . '_' . $pageTranslation->getLocale(), $route);
        }

        return $collection;
    }

    public function getRouteByName(string $name): SymfonyRoute
    {
        $pageTranslation = null;

        $lastUnderscorePosition = strrpos($name, '_');
        if ($lastUnderscorePosition !== false)
        {
            $routeName = substr($name, 0, $lastUnderscorePosition);
            $locale = substr($name, $lastUnderscorePosition + 1);

            $pageTranslation = $this->entityManager->getRepository(PageTranslation::class)
                ->createQueryBuilder('pt')
                ->leftJoin('pt.page', 'p')
                ->andWhere('pt.locale = :locale')->setParameter('locale', $locale)
                ->andWhere('p.routeName = :routeName')->setParameter('routeName', $routeName)
                ->getQuery()
                ->getOneOrNullResult();
        }

        if (!$pageTranslation) {
            throw new RouteNotFoundException(sprintf('Route "%s" not found', $name));
        }

        return new Route($pageTranslation->getSlug(), [
            '_controller' => 'kikwik_page.controller.page_controller::show',
            'pageTranslation' => $pageTranslation
        ]);
    }

    public function getRoutesByNames(?array $names = null): iterable
    {
        $collection = new RouteCollection();

        if(is_array($names)) {
            foreach ($names as $name) {
                try {
                    $route = $this->getRouteByName($name);
                    $collection->add($name, $route);
                } catch (RouteNotFoundException $e) {
                    // Handle the exception or ignore it
                }
            }
        }

        return $collection;
    }

}