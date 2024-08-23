<?php

namespace Kikwik\PageBundle\Block;

abstract class BaseBlockComponent implements BlockComponentInterface
{
    private $parameters = [];

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getName(): string
    {
        $class = get_class($this);
        $strippedClass = str_replace(['Kikwik\PageBundle\Twig\Components','App\Twig\Components\\'],['KikwikPage',''],$class);

        return str_replace('\\',':',$strippedClass);
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

}