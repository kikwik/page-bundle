<?php

namespace Kikwik\PageBundle\Block;

use Kikwik\PageBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

interface BlockComponentInterface
{
    public function getComponentName(): string;
    public function getComponentLabel(): string;

    public function setBlock(BlockInterface $block): void;

    public function getBlock(): BlockInterface;

    public function buildEditForm(FormInterface $form): void;
}