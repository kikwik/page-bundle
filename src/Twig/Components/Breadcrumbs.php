<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Entity\PageTranslation;
use Symfony\Component\HttpFoundation\RequestStack;

class Breadcrumbs
{
    public array $extraLink = [];

    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function getPageTranslation(): ?PageTranslation
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    public function getUrls(): array
    {
        $result = [];
        $pageTranslation = $this->getPageTranslation();
        if ($pageTranslation)
        {
            array_unshift($result, [$pageTranslation->getUrl() => $pageTranslation->getTitle()]);
            while ($pageTranslation->getParent())
            {
                $pageTranslation = $pageTranslation->getParent();
                array_unshift($result, [$pageTranslation->getUrl() => $pageTranslation->getTitle()]);
            }
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
}