<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LocaleSwitcher
{
    public function __construct(
        private RequestStack          $requestStack,
        private UrlGeneratorInterface $urlGenerator,
        private array                 $enabledLocales,
    )
    {
    }

    public function getPageTranslation(): ?PageTranslationInterface
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    public function getCurrentLocale(): string
    {
        return $this->requestStack->getCurrentRequest()->getLocale();
    }

    public function getExtraSlug(): string
    {
        return $this->requestStack->getCurrentRequest()->get('extraSlug')
            ? '/'.$this->requestStack->getCurrentRequest()->get('extraSlug')
            : '';
    }

    public function getUrls(): array
    {
        $result = [];
        if($this->getPageTranslation())
        {
            $page = $this->getPageTranslation()->getPage();
            /** @var PageTranslationInterface $translation */
            foreach($page->getTranslations() as $translation)
            {
                if($translation->getId() && $translation->isEnabled())
                {
                    $result[$translation->getLocale()] = $translation->getUrl().$this->getExtraSlug();
                }
            }
        }
        else
        {
            $route = $this->requestStack->getCurrentRequest()->get('_route');
            if($route)
            {
                $routeParams = $this->requestStack->getCurrentRequest()->get('_route_params',[]);
                foreach($this->enabledLocales as $locale)
                {
                    $result[$locale] = $this->urlGenerator->generate($route, array_merge($routeParams,['_locale' => $locale]));
                }
            }
        }
        return $result;
    }
}