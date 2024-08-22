<?php

namespace Kikwik\PageBundle\Twig\Components;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class AdminBar
{
    public function __construct(
        private RequestStack                  $requestStack,
        private UrlGeneratorInterface         $urlGenerator,
        private AuthorizationCheckerInterface $authorizationChecker,
        private string $adminRole
    )
    {
    }

    public function getEditLink(): ?string
    {
        $pageTranslation = $this->requestStack->getCurrentRequest()->get('pageTranslation');
        $isGranted = !$this->adminRole || $this->authorizationChecker->isGranted($this->adminRole);
        return $pageTranslation && $isGranted
            ? $this->urlGenerator->generate('kikwik_page_admin_update', ['id' => $pageTranslation->getPage()->getId()])
            : null;
    }
}