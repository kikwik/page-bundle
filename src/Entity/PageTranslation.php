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
use Gedmo\Sluggable\Handler\TreeSlugHandler;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
#[Table(name: 'kw_page__page_translation')]
#[ORM\HasLifecycleCallbacks]
class PageTranslation
{
    use TimestampableEntity;
    use BlameableEntity;
    use IpTraceableEntity;

    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    #[ORM\Id()]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Page $page = null;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: false)]
    protected ?string $locale = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\NotBlank()]
    protected ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    protected bool $isEnabled = true;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['title'])]
    #[Gedmo\SlugHandler(class: TreeSlugHandler::class, options: [
        'parentRelationField' => 'parent',
        'separator' => '/',
    ])]
    protected ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: pageTranslation::class)]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected ?pageTranslation $parent = null;

    /**
     * @var Collection<int, Block>
     */
    #[ORM\OneToMany(targetEntity: Block::class, mappedBy: 'pageTranslation', cascade: ['persist','remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Assert\Valid]
    protected Collection $blocks;

    /**************************************/
    /* CUSTOM METHODS                     */
    /**************************************/

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSlug()
    {
        // Se la pagina è root, forza lo slug a essere uguale al locale
        if ($this->parent === null) {
            $this->slug = $this->locale;
        }
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): PageTranslation
    {
        $this->locale = $locale;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): PageTranslation
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): PageTranslation
    {
        $this->description = $description;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): PageTranslation
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): PageTranslation
    {
        $this->slug = $slug;
        return $this;
    }

    public function getParent(): ?PageTranslation
    {
        return $this->parent;
    }

    public function setParent(?PageTranslation $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Block>
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(Block $block): self
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks[] = $block;
            $block->setPageTranslation($this);
        }

        return $this;
    }

    public function removeBlock(Block $block): self
    {
        if ($this->blocks->removeElement($block)) {
            if ($block->getPageTranslation() === $this) {
                $block->setPageTranslation(null);
            }
        }

        return $this;
    }
}