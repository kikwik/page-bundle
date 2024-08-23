<?php

namespace Kikwik\PageBundle\Service;

use Kikwik\PageBundle\Block\BlockComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BlockComponentProvider
{
    public function __construct(
        private ContainerInterface $container,
        private iterable $twigComponents
    )
    {
    }

    public function getBlockComponentChoices(): array
    {
        $choices = [];

        foreach ($this->getBlockComponents() as $component) {
            $choices[$component->getLabel()] = $component->getName();
        }

        return $choices;
    }

    public function getBlockComponents(): array
    {
        $components = [];

        foreach ($this->twigComponents as $component)
        {
            if($component instanceof BlockComponentInterface)
            {
                $components[$component->getName()] = $component;
            }
        }

        return $components;
    }

    public function getBlockComponent(string $name): ?BlockComponentInterface
    {
        return $this->getBlockComponents()[$name] ?? null;
    }
}