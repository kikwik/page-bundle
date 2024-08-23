<?php

namespace Kikwik\PageBundle\Block;

interface BlockComponentInterface
{
    public function getName(): string;
    public function getLabel(): string;

    public function setParameters(array $parameters): void;
    public function getParameters(): array;
}