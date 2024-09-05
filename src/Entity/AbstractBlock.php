<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\IpTraceable\Traits\IpTraceableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kikwik\PageBundle\Model\BlockInterface;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
class AbstractBlock implements BlockInterface
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

    #[ORM\ManyToOne(inversedBy: 'blocks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Gedmo\SortableGroup()]
    protected ?PageTranslationInterface $pageTranslation = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank()]
    protected ?string $component = null;

    #[ORM\Column(type: Types::JSON, nullable: false)]
    protected array $parameters = [];

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    protected bool $isEnabled = true;

    #[ORM\Column(type: Types::INTEGER)]
    #[Gedmo\SortablePosition()]
    protected ?int $position = null;

    /**************************************/
    /* CUSTOM METHODS                     */
    /**************************************/



    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPageTranslation(): ?PageTranslationInterface
    {
        return $this->pageTranslation;
    }

    public function setPageTranslation(?PageTranslationInterface $pageTranslation): BlockInterface
    {
        $this->pageTranslation = $pageTranslation;
        return $this;
    }

    public function getComponent(): ?string
    {
        return $this->component;
    }

    public function setComponent(?string $component): BlockInterface
    {
        $this->component = $component;
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): BlockInterface
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): BlockInterface
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): BlockInterface
    {
        $this->position = $position;
        return $this;
    }
}