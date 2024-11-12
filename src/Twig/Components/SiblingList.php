<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class SiblingList
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public ?PageInterface $page = null;

    public bool $showParentLink = false;

    /**
     * @return PageTranslationInterface[]
     */
    public function getPageSiblings(): array
    {
        $result = [];
        $parent = $this->getPage()?->getParent();
        if($parent)
        {
            foreach($parent->getChildren() as $siblingPage)
            {
                $siblingTranslation = $siblingPage->getTranslation($this->getLocale());
                if($siblingTranslation && $siblingTranslation->isEnabled())
                {
                    $result[] = $siblingPage->getTranslation($this->getLocale());
                }
            }
        }

        return $result;
    }

    public function getPageTranslation(): ?PageTranslationInterface
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    public function getPage(): ?PageInterface
    {
        return $this->page ?? $this->getPageTranslation()->getPage();
    }

    public function getParentTranslation(): ?PageTranslationInterface
    {
        $parent = $this->getPage()?->getParent();
        if($parent && $parent->getTranslation($this->getLocale()) && $parent->getTranslation($this->getLocale())->isEnabled())
        {
            return $parent->getTranslation($this->getLocale());
        }
        return null;
    }

    public function getLocale(): string
    {
        return $this->getPageTranslation()
            ? $this->getPageTranslation()->getLocale()
            : $this->requestStack->getMainRequest()->getLocale();
    }
}