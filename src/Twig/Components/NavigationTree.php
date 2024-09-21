<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class NavigationTree
{
    public int $fromLevel = 0;

    public ?PageInterface $rootNode = null;

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
        if($this->rootNode)
        {
            return $this->pageRepository->getTreeWithTranslations($this->rootNode, $this->getLocale());
        }
        else
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
    }

    /**
     * @return PageInterface[]
     */
    public function getPath(): array
    {
        return $this->pageRepository->getPathWithTranslations($this->getPage(), $this->getLocale());
    }

    public function getPage(): ?PageInterface
    {
        return $this->getPageTranslation()?->getPage();
    }

    public function getLocale(): string
    {
        return $this->getPageTranslation()
            ? $this->getPageTranslation()->getLocale()
            : $this->requestStack->getMainRequest()->getLocale();
    }

}