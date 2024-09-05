<?php

namespace Kikwik\PageBundle\Model;

use Doctrine\Common\Collections\Collection;

interface PageInterface
{
    public function hasTranslation(string $locale): bool;
    public function getTranslation(string $locale): ?PageTranslationInterface;

    public function getId(): ?int;

    public function getName(): ?string;
    public function setName(string $name): PageInterface;

    public function getRouteName(): ?string;
    public function setRouteName(?string $routeName): PageInterface;

    public function getLft(): ?int;
    public function setLft(?int $lft): PageInterface;

    public function getLvl(): ?int;
    public function setLvl(?int $lvl): PageInterface;

    public function getRgt(): ?int;
    public function setRgt(?int $rgt): PageInterface;

    public function getRoot(): ?PageInterface;
    public function setRoot(?PageInterface $root): PageInterface;

    public function getParent(): ?PageInterface;
    public function setParent(?PageInterface $parent): PageInterface;

    public function getChildren(): Collection;
    public function setChildren(Collection $children): PageInterface;

    /**
     * @return Collection<int, PageTranslationInterface>
     */
    public function getTranslations(): Collection;
    public function addTranslation(PageTranslationInterface $translation): PageInterface;
    public function removeTranslation(PageTranslationInterface $translation): PageInterface;
}