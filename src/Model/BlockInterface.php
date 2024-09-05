<?php

namespace Kikwik\PageBundle\Model;

interface BlockInterface
{
    public function getId(): ?int;

    public function getPageTranslation(): ?PageTranslationInterface;
    public function setPageTranslation(?PageTranslationInterface $pageTranslation): BlockInterface;

    public function getComponent(): ?string;
    public function setComponent(?string $component): BlockInterface;

    public function getParameters(): array;
    public function setParameters(array $parameters): BlockInterface;

    public function isEnabled(): bool;
    public function setIsEnabled(bool $isEnabled): BlockInterface;

    public function getPosition(): ?int;
    public function setPosition(?int $position): BlockInterface;
}