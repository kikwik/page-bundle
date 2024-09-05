<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Kikwik\PageBundle\Model\PageTranslationInterface;

#[ORM\Entity()]
#[Table(name: 'kw_page__page_translation')]
class PageTranslation extends AbstractPageTranslation implements PageTranslationInterface
{

}