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
            $tmpResult = [];
            // list translations from current page (sblings of $this->getPageTranslation())
            $page = $this->getPageTranslation()->getPage();
            /** @var PageTranslationInterface $translation */
            foreach($page->getTranslations() as $translation)
            {
                if($translation->getId() && $translation->isEnabled() && in_array($translation->getLocale(), $this->enabledLocales))
                {
                    $tmpResult[$translation->getLocale()] = $translation->getUrl().$this->getExtraSlug();
                }
            }
            // sort links by the $this->enabledLocales order
            foreach($this->enabledLocales as $locale)
            {
                if(isset($tmpResult[$locale]))
                {
                    $result[$locale] = $tmpResult[$locale];
                }
            }
        }
        else
        {
            // list translations from $this->enabledLocales
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