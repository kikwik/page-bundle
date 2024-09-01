<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Entity\Page;
use Kikwik\PageBundle\Entity\PageTranslation;
use Symfony\Component\HttpFoundation\RequestStack;

class ChildList
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function getPageTranslation(): ?PageTranslation
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    /**
     * @return PageTranslation[]
     */
    public function getPageChildren(): array
    {
        $result = [];
        $pageTranslation = $this->getPageTranslation();
        if($pageTranslation)
        {
            $page = $pageTranslation->getPage();
            $locale = $this->getPageTranslation()->getLocale();
            /** @var Page $childPage */
            foreach($page->getChildren() as $childPage)
            {
                if($childPage->hasTranslation($locale))
                {
                    $result[] = $childPage->getTranslation($locale);
                }
            }
        }

        return $result;
    }
}