<?php

namespace Kikwik\PageBundle\Block;

use Kikwik\PageBundle\Entity\Block;

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

    private ?Block $block = null;

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function setBlock(Block $block): void
    {
        $this->block = $block;
    }

    public function get(string $parameterKey)
    {
        return $this->block?->getParameters()[$parameterKey] ?? null;
    }



}