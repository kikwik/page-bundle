<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class ChildList
{
    public function __construct(
        private RequestStack $requestStack,
        private PageRepository $pageRepository,
    )
    {
    }

    /**
     * @return PageTranslationInterface[]
     */
    public function getPageChildren(): array
    {
        $result = [];
        $children = $this->pageRepository->getChildrenWithTranslations($this->getPage(), $this->getLocale());
        foreach($children as $childPage)
        {
            $result[] = $childPage->getTranslation($this->getLocale());
        }

        return $result;
    }

    private function getPageTranslation(): ?PageTranslationInterface
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    private function getPage(): ?PageInterface
    {
        return $this->getPageTranslation()->getPage();
    }

    private function getLocale(): string
    {
        return $this->getPageTranslation()->getLocale();
    }
}