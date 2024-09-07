<?php

namespace Kikwik\PageBundle\Service;

use Kikwik\PageBundle\Block\BlockComponentInterface;

class BlockComponentProvider
{
    public function __construct(
        private iterable $twigComponents
    )
    {
    }

    public function getBlockComponentChoices(): array
    {
        $choices = [];

        foreach ($this->getBlockComponents() as $component) {
            $choices[$component->getComponentLabel()] = $component->getComponentName();
        }

        return $choices;
    }

    /**
     * @return BlockComponentInterface[]
     */
    public function getBlockComponents(): array
    {
        $components = [];

        foreach ($this->twigComponents as $component)
        {
            if($component instanceof BlockComponentInterface)
            {
                $components[$component->getComponentName()] = $component;
            }
        }

        return $components;
    }

    public function getBlockComponent(string $name): ?BlockComponentInterface
    {
        return $this->getBlockComponents()[$name] ?? null;
    }
}