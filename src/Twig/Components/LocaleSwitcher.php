<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Block\BaseBlockComponent;
use Kikwik\PageBundle\Entity\PageTranslation;
use Symfony\Component\HttpFoundation\RequestStack;

class LocaleSwitcher extends BaseBlockComponent
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function getDefaultValues(): array
    {
        return [];
    }

    public function getPageTranslation(): ?PageTranslation
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    public function getCurrentLocale(): string
    {
        return $this->requestStack->getCurrentRequest()->getLocale();
    }

    public function getUrls(): array
    {
        $result = [];
        $page = $this->getPageTranslation()->getPage();
        /** @var PageTranslation $translation */
        foreach($page->getTranslations() as $translation)
        {
            $result[$translation->getLocale()] = '/'.$translation->getSlug();
        }
        return $result;
    }
}