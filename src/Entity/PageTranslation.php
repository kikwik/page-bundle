<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kikwik\PageBundle\Model\PageTranslationInterface;

#[ORM\Entity()]
#[ORM\Table(name: 'kw_page__page_translation')]
#[ORM\HasLifecycleCallbacks]
class PageTranslation extends AbstractPageTranslation implements PageTranslationInterface
{
    use \App\Entity\PageTranslationTrait;
}