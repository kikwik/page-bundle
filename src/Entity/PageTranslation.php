<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kikwik\PageBundle\Model\PageTranslationInterface;
use Kikwik\PageBundle\Repository\PageTranslationRepository;

#[ORM\Entity()]
#[ORM\Table(name: 'kw_page__page_translation')]
class PageTranslation extends AbstractPageTranslation implements PageTranslationInterface
{
}