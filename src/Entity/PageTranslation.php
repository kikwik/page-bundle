<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\IpTraceable\Traits\IpTraceableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sluggable\Handler\TreeSlugHandler;
use Gedmo\Timestampable\Traits\TimestampableEntity;

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

    #[ORM\Column(type: Types::TEXT, length: 5, nullable: false)]
    protected ?string $locale = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    protected ?string $title = null;

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

    /**************************************/
    /* CUSTOM METHODS                     */
    /**************************************/
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
}