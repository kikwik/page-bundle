<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class Breadcrumbs
{
    public ?string $firstItemLabel = null;

    public array $extraLink = [];

    public function __construct(
        private RequestStack $requestStack,
        private PageRepository $pageRepository,
    )
    {
    }


    public function getUrls(): array
    {
        // Find path for current page and build array with ['url'=>'title']
        $result = [];
        $path = $this->pageRepository->getPathWithTranslations($this->getPage(), $this->getLocale());
        foreach($path as $index => $page)
        {
            $pageTranslation = $page->gettranslation($this->getLocale());
            $result[] = [$pageTranslation->getUrl() => $pageTranslation->getTitle()];
        }

        // Overrides the label of the first element
        if(count($result) && $this->firstItemLabel)
        {
            $firstKey = array_key_first($result[0]);
            $result[0][$firstKey] = $this->firstItemLabel;
        }

        // Flat the array to get key-value pairs
        $flattenedResult = [];
        foreach ($result as $item)
        {
            $flattenedResult += $item;
        }

        $flattenedResult += $this->extraLink;

        return $flattenedResult;
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