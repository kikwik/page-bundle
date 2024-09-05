<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminBar
{
    public function __construct(
        private RequestStack                  $requestStack,
        private AuthorizationCheckerInterface $authorizationChecker,
        private string                        $adminRole
    )
    {
    }

    public function isGranted(): bool
    {
        return !$this->adminRole || $this->authorizationChecker->isGranted($this->adminRole);
    }

    public function getPageTranslation(): ?PageTranslationInterface
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    public function getPage(): ?PageInterface
    {
        return $this->getPageTranslation()?->getPage();
    }
}