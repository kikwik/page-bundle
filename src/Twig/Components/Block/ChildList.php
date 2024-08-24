<?php

namespace Kikwik\PageBundle\Twig\Components\Block;

use Kikwik\PageBundle\Block\BaseBlockComponent;
use Kikwik\PageBundle\Entity\Page;
use Kikwik\PageBundle\Entity\PageTranslation;
use Kikwik\PageBundle\Repository\PageTranslationRepository;

class ChildList extends BaseBlockComponent
{
    public function __construct(
        private PageTranslationRepository $pageTranslationRepository,
    )
    {
    }

    public function getDefaultValues(): array
    {
        return [];
    }

    /**
     * @return PageTranslation[]
     */
    public function getPageChildren(): array
    {
        $result = [];
        $page = $this->block->getPageTranslation()->getPage();
        $locale = $this->block->getPageTranslation()->getLocale();
        /** @var Page $childPage */
        foreach($page->getChildren() as $childPage)
        {
            if($childPage->hasTranslation($locale))
            {
                $result[] = $childPage->getTranslation($locale);
            }
        }
        return $result;
    }
}