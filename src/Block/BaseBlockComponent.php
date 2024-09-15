<?php

namespace Kikwik\PageBundle\Block;

use Kikwik\PageBundle\Model\BlockInterface;
use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;

abstract class BaseBlockComponent implements BlockComponentInterface
{
    public function getComponentName(): string
    {
        $class = get_class($this);
        $strippedClass = str_replace(['Kikwik\PageBundle\Twig\Components','App\Twig\Components\\'],['KikwikPage',''],$class);

        return str_replace('\\',':',$strippedClass);
    }

    public function getComponentLabel(): string
    {
        return $this->getComponentName();
    }

    protected ?BlockInterface $block = null;

    public function getBlock(): BlockInterface
    {
        return $this->block;
    }

    public function setBlock(BlockInterface $block): void
    {
        $this->block = $block;
    }

    public function getPageTranslation(): PageTranslationInterface
    {
        return $this->block->getPageTranslation();
    }

    public function getPage(): PageInterface
    {
        return $this->block->getPageTranslation()->getPage();
    }

    public function getLocale(): string
    {
        return $this->getPageTranslation()->getLocale();
    }

    public function get(string $parameterKey)
    {
        return $this->block?->getParameters()[$parameterKey] ?? null;
    }



}