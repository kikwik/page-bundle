<?php

namespace Kikwik\PageBundle\Routing;

use Kikwik\PageBundle\Repository\PageTranslationRepository;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

class PageTranslationRouteProvider implements RouteProviderInterface
{
    public function __construct(
        private PageTranslationRepository $pageTranslationRepository,
    )
    {
    }

    public function getRouteCollectionForRequest(Request $request): RouteCollection
    {
        $collection = new RouteCollection();

        // Ottieni lo slug dalla richiesta
        $slug = $request->getPathInfo();
        $slug = trim($slug, '/');

        // Trova la traduzione della pagina con lo slug completo
        $pageTranslation = $this->pageTranslationRepository->findOneBySlugJoinPage($slug);

        // Se non è trovata, cerca per slug parziali
        if (!$pageTranslation)
        {
            $slugParts = explode('/', $slug);
            while (count($slugParts) > 1)
            {
                array_pop($slugParts);
                $partialSlug = implode('/', $slugParts);
                $pageTranslation = $this->pageTranslationRepository->findOneBySlugJoinPage($partialSlug);
                if ($pageTranslation)
                {
                    break;
                }
            }
        }

        if ($pageTranslation)
        {
            $remainingSlug = str_replace($pageTranslation->getSlug(), '', $slug);
            $additionalParameters = array_filter(explode('/', $remainingSlug));

            $route = new Route($pageTranslation->getSlug().'/{extraSlug}', [
                '_controller' => 'kikwik_page.controller.page_controller::show',
                'pageTranslation' => $pageTranslation,
                'extraSlug' => implode('/', $additionalParameters), // Converti in stringa per il routing
            ], [
                'extraSlug' => '.*', // Pattern per matchare i parametri addizionali
            ]);

            $collection->add($pageTranslation->getPage()->getRouteName().'_'.$pageTranslation->getLocale(), $route);
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

            $pageTranslation = $this->pageTranslationRepository
                ->createQueryBuilder('pt')
                ->leftJoin('pt.page', 'p')->addSelect('p')
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