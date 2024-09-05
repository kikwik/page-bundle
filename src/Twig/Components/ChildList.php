<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ChildList
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function getPageTranslation(): ?PageTranslationInterface
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    /**
     * @return PageTranslationInterface[]
     */
    public function getPageChildren(): array
    {
        $result = [];
        $pageTranslation = $this->getPageTranslation();
        if($pageTranslation)
        {
            $page = $pageTranslation->getPage();
            $locale = $this->getPageTranslation()->getLocale();
            /** @var PageInterface $childPage */
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