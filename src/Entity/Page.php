<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\IpTraceable\Traits\IpTraceableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
#[Table(name: 'kw_page__page')]
#[UniqueEntity(fields: ['name'])]
#[Gedmo\Tree(type: 'nested')]
class Page implements PageInterface
{
    use TimestampableEntity;
    use BlameableEntity;
    use IpTraceableEntity;

    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank()]
    protected ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Gedmo\Slug(fields: ['name'], updatable: false, unique: true)]
    protected ?string $routeName = null;

    #[Gedmo\TreeLeft]
    #[ORM\Column(name: 'lft', type: Types::INTEGER)]
    protected ?int $lft = null;

    #[Gedmo\TreeLevel]
    #[ORM\Column(name: 'lvl', type: Types::INTEGER)]
    protected ?int $lvl = null;

    #[Gedmo\TreeRight]
    #[ORM\Column(name: 'rgt', type: Types::INTEGER)]
    protected ?int $rgt = null;

    #[Gedmo\TreeRoot]
    #[ORM\ManyToOne(targetEntity: PageInterface::class)]
    #[ORM\JoinColumn(name: 'tree_root', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected ?PageInterface $root = null;

    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(targetEntity: PageInterface::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected ?PageInterface $parent = null;

    #[ORM\OneToMany(targetEntity: PageInterface::class, mappedBy: 'parent')]
    #[ORM\OrderBy(['lft' => 'ASC'])]
    protected Collection $children;

    /**
     * @var Collection<int, PageTranslationInterface>
     */
    #[ORM\OneToMany(targetEntity: PageTranslationInterface::class, mappedBy: 'page', cascade: ['persist','remove'], orphanRemoval: true)]
    #[Assert\Valid]
    protected Collection $translations;

    /**************************************/
    /* CUSTOM METHODS                     */
    /**************************************/

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function hasTranslation(string $locale): bool
    {
        foreach($this->translations as $translation) {
            if($translation->getLocale() === $locale) {
                return true;
            }
        }
        return false;
    }

    public function getTranslation(string $locale): ?PageTranslationInterface
    {
        foreach($this->translations as $translation) {
            if($translation->getLocale() === $locale) {
                return $translation;
            }
        }

        $parentTranslation = $this->getParent()?->getTranslation($locale);
        $newTranslation = new PageTranslation(); // TODO: questo non va bene, dannazione!
        $newTranslation->setLocale($locale);
        $newTranslation->setParent($parentTranslation);
        $this->addTranslation($newTranslation);
        return $newTranslation;
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): PageInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function setRouteName(?string $routeName): PageInterface
    {
        $this->routeName = $routeName;
        return $this;
    }

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(?int $lft): PageInterface
    {
        $this->lft = $lft;

        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(?int $lvl): PageInterface
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(?int $rgt): PageInterface
    {
        $this->rgt = $rgt;

        return $this;
    }

    public function getRoot(): ?PageInterface
    {
        return $this->root;
    }

    public function setRoot(?PageInterface $root): PageInterface
    {
        $this->root = $root;

        return $this;
    }

    public function getParent(): ?PageInterface
    {
        return $this->parent;
    }

    public function setParent(?PageInterface $parent): PageInterface
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setChildren(Collection $children): PageInterface
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return Collection<int, PageTranslationInterface>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(PageTranslationInterface $translation): PageInterface
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setPage($this);
        }

        return $this;
    }

    public function removeTranslation(PageTranslationInterface $translation): PageInterface
    {
        if ($this->translations->removeElement($translation)) {
            if ($translation->getPage() === $this) {
                $translation->setPage(null);
            }
        }

        return $this;
    }


}
