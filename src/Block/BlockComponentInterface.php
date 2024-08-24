<?php

namespace Kikwik\PageBundle\Block;

use Kikwik\PageBundle\Entity\Block;
use Symfony\Component\Form\FormBuilderInterface;

interface BlockComponentInterface
{
    public function getComponentName(): string;
    public function getComponentLabel(): string;

    public function setBlock(Block $block): void;

    public function getBlock(): Block;

    public function getDefaultValues(): array;
}