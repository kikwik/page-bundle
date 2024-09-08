<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\IpTraceable\Traits\IpTraceableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sluggable\Handler\TreeSlugHandler;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kikwik\PageBundle\Model\BlockInterface;
use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractPageTranslation implements PageTranslationInterface
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
    protected ?PageInterface $page = null;

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

    #[ORM\ManyToOne(targetEntity: PageTranslationInterface::class)]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected ?PageTranslationInterface $parent = null;

    /**
     * @var Collection<int, BlockInterface>
     */
    #[ORM\OneToMany(targetEntity: BlockInterface::class, mappedBy: 'pageTranslation', cascade: ['persist','remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Assert\Valid()]
    protected Collection $blocks;

    /**************************************/
    /* CUSTOM METHODS                     */
    /**************************************/

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    public function getUrl(): string
    {
        return '/'.$this->slug;
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

    public function getPage(): ?PageInterface
    {
        return $this->page;
    }

    public function setPage(?PageInterface $page): PageTranslationInterface
    {
        $this->page = $page;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): PageTranslationInterface
    {
        $this->locale = $locale;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): PageTranslationInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): PageTranslationInterface
    {
        $this->description = $description;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): PageTranslationInterface
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): PageTranslationInterface
    {
        $this->slug = $slug;
        return $this;
    }

    public function getParent(): ?PageTranslationInterface
    {
        return $this->parent;
    }

    public function setParent(?PageTranslationInterface $parent): PageTranslationInterface
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, BlockInterface>
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(BlockInterface $block): PageTranslationInterface
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks[] = $block;
            $block->setPageTranslation($this);
        }

        return $this;
    }

    public function removeBlock(BlockInterface $block): PageTranslationInterface
    {
        if ($this->blocks->removeElement($block)) {
            if ($block->getPageTranslation() === $this) {
                $block->setPageTranslation(null);
            }
        }

        return $this;
    }
}