<?php

namespace Kikwik\PageBundle\Model;

use Doctrine\Common\Collections\Collection;

interface PageTranslationInterface
{
    public function getUrl(): string;

    public function updateSlug();

    public function getId(): ?int;

    public function getPage(): ?PageInterface;
    public function setPage(?PageInterface $page): PageTranslationInterface;

    public function getLocale(): ?string;
    public function setLocale(?string $locale): PageTranslationInterface;

    public function getTitle(): ?string;
    public function setTitle(?string $title): PageTranslationInterface;

    public function getDescription(): ?string;
    public function setDescription(?string $description): PageTranslationInterface;

    public function isEnabled(): bool;
    public function setIsEnabled(bool $isEnabled): PageTranslationInterface;

    public function getSlug(): ?string;
    public function setSlug(?string $slug): PageTranslationInterface;

    public function getParent(): ?PageTranslationInterface;
    public function setParent(?PageTranslationInterface $parent): PageTranslationInterface;

    /**
     * @return Collection<int, BlockInterface>
     */
    public function getBlocks(): Collection;

    public function addBlock(BlockInterface $block): PageTranslationInterface;

    public function removeBlock(BlockInterface $block): PageTranslationInterface;
}