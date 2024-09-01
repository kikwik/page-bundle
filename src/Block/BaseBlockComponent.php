<?php

namespace Kikwik\PageBundle\Block;

use Kikwik\PageBundle\Entity\Block;
use Kikwik\PageBundle\Entity\Page;
use Kikwik\PageBundle\Entity\PageTranslation;

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

    protected ?Block $block = null;

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function setBlock(Block $block): void
    {
        $this->block = $block;
    }

    public function getPageTranslation(): PageTranslation
    {
        return $this->block->getPageTranslation();
    }

    public function getPage(): Page
    {
        return $this->block->getPageTranslation()->getPage();
    }

    public function get(string $parameterKey)
    {
        return $this->block?->getParameters()[$parameterKey] ?? null;
    }



}