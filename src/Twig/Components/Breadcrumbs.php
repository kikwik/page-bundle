<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Breadcrumbs
{
    public ?string $firstItemLabel = null;

    public array $extraLink = [];

    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function getPageTranslation(): ?PageTranslationInterface
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    public function getUrls(): array
    {
        $result = [];
        $pageTranslation = $this->getPageTranslation();
        if ($pageTranslation)
        {
            // Costruisce il path all'indietro
            array_unshift($result, [$pageTranslation->getUrl() => $pageTranslation->getTitle()]);
            while ($pageTranslation->getParent())
            {
                $pageTranslation = $pageTranslation->getParent();
                array_unshift($result, [$pageTranslation->getUrl() => $pageTranslation->getTitle()]);
            }
        }
        if(count($result) && $this->firstItemLabel)
        {
            // Sovrascrive la label del primo elemento
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
}