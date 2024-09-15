<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class NavigationTree
{
    public int $fromLevel = 0;

    public function __construct(
        private RequestStack $requestStack,
        private PageRepository $pageRepository,
    )
    {
    }

    public function getPageTranslation(): ?PageTranslationInterface
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    public function getTree()
    {
        $path = $this->getPath();
        foreach($path as $page)
        {
            if($page->getLvl() == $this->fromLevel)
            {
                return $this->pageRepository->getTreeWithTranslations($page, $this->getLocale());
            }
        }

    }

    /**
     * @return PageInterface[]
     */
    private function getPath(): array
    {
        return $this->pageRepository->getPathWithTranslations($this->getPage(), $this->getLocale());
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