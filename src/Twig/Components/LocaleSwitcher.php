<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LocaleSwitcher
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

    public function getCurrentLocale(): string
    {
        return $this->requestStack->getCurrentRequest()->getLocale();
    }

    public function getExtraSlug(): string
    {
        return $this->requestStack->getCurrentRequest()->get('extraSlug')
            ? '/'.$this->requestStack->getCurrentRequest()->get('extraSlug')
            : '';
    }

    public function getUrls(): array
    {
        $result = [];
        $page = $this->getPageTranslation()->getPage();
        /** @var PageTranslationInterface $translation */
        foreach($page->getTranslations() as $translation)
        {
            if($translation->getId() && $translation->isEnabled())
            {
                $result[$translation->getLocale()] = $translation->getUrl().$this->getExtraSlug();
            }
        }
        return $result;
    }
}