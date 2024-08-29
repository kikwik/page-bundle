<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Entity\Page;
use Kikwik\PageBundle\Entity\PageTranslation;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminBar
{
    public function __construct(
        private RequestStack                  $requestStack,
        private AuthorizationCheckerInterface $authorizationChecker,
        private string $adminRole
    )
    {
    }

    public function isGranted(): bool
    {
        return !$this->adminRole || $this->authorizationChecker->isGranted($this->adminRole);
    }

    public function getPageTranslation(): ?PageTranslation
    {
        return $this->requestStack->getCurrentRequest()->get('pageTranslation');
    }

    public function getPage(): ?Page
    {
        return $this->getPageTranslation()?->getPage();
    }
}