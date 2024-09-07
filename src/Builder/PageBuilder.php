<?php

namespace Kikwik\PageBundle\Builder;


use Kikwik\PageBundle\Model\BlockInterface;
use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;

class PageBuilder
{
    public function __construct(
        private array $enabledLocales,
        private string $pageClassName,
        private string $pageTranslationClassName,
        private string $blockClassName,
    )
    {
    }

    private ?string $name = null;
    private array $titles = [];
    private array $descriptions = [];
    private mixed $parent = null;
    private array $components = [];

    public function setName(string $pageName): self
    {
        $this->name = $pageName;
        return $this;
    }

    public function setTitle(string $locale, string $title): self
    {
        $this->titles[$locale] = $title;
        return $this;
    }

    public function setDescription(string $locale, string $description): self
    {
        $this->descriptions[$locale] = $description;
        return $this;
    }

    public function setParent(?PageInterface $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function addBlock(string $componentName, array $componentParams = []): self
    {
        $this->components[] = [
            'component'=>$componentName,
            'parameters' => $componentParams
        ];
        return $this;
    }

    public function getPage(): PageInterface
    {
        /** @var PageInterface $page */
        $page = new $this->pageClassName();
        $page->setName($this->name);
        $page->setParent($this->parent);
        foreach($this->enabledLocales as $locale)
        {
            /** @var PageTranslationInterface $pageTranslation */
            $pageTranslation = new $this->pageTranslationClassName();
            $pageTranslation->setLocale($locale);
            if(isset($this->titles[$locale]))
            {
                $pageTranslation->setTitle($this->titles[$locale]);
            }
            if(isset($this->descriptions[$locale]))
            {
                $pageTranslation->setDescription($this->descriptions[$locale]);
            }
            $pageTranslation->setParent($page->getParent()?->getTranslation($locale));
            $page->addTranslation($pageTranslation);

            foreach($this->components as $component)
            {
                /** @var BlockInterface $block */
                $block = new $this->blockClassName();
                $block->setComponent($component['component']);
                $block->setParameters($component['parameters']);
                $pageTranslation->addBlock($block);
            }
        }
        return $page;
    }
}