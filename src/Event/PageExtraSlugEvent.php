<?php

namespace Kikwik\PageBundle\Event;

use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class PageExtraSlugEvent extends Event
{
    public const NAME = 'kikwik_page.extra_slug';

    private ?Response $response = null;

    public function __construct(
        private PageTranslationInterface $pageTranslation,
        private string $extraSlug
    ) {
    }

    public function getPageTranslation(): PageTranslationInterface
    {
        return $this->pageTranslation;
    }

    public function getExtraSlug(): string
    {
        return $this->extraSlug;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}