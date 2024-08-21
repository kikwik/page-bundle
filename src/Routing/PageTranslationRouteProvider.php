<?php

namespace Kikwik\PageBundle\Routing;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
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
                '_controller' => 'Kikwik\PageBundle\Controller\PageController::show', // Adjust the controller path as necessary
                'pageTranslation' => $pageTranslation
            ]);

            $collection->add('kikwik_page_translation_' . $pageTranslation->getId(), $route);
        }

        return $collection;
    }

    public function getRouteByName(string $name): SymfonyRoute
    {
        // Retrieve the PageTranslation by some criteria
        $pageTranslation = $this->entityManager->getRepository(PageTranslation::class)->find($name);

        if (!$pageTranslation) {
            throw new RouteNotFoundException(sprintf('Route "%s" not found', $name));
        }

        return new Route($pageTranslation->getSlug(), [
            '_controller' => 'Kikwik\PageBundle\Controller\PageController::show',
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