<?php

namespace Kikwik\PageBundle\Block;

use Symfony\Component\Form\FormBuilderInterface;

interface BlockComponentInterface
{
    public function getName(): string;
    public function getLabel(): string;

    public function setParameters(array $parameters): void;
    public function getParameters(): array;

    public function getDefaultValues(): array;
}