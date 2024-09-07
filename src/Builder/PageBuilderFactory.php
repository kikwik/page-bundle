<?php

namespace Kikwik\PageBundle\Builder;

class PageBuilderFactory
{
    public function __construct(
        private array $enabledLocales,
        private string $pageClassName,
        private string $pageTranslationClassName,
        private string $blockClassName,
    )
    {
    }

    public function createPageBuilder(): PageBuilder
    {
        return new PageBuilder(
            $this->enabledLocales,
            $this->pageClassName,
            $this->pageTranslationClassName,
            $this->blockClassName,
        );
    }
}