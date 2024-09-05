<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kikwik\PageBundle\Model\BlockInterface;

#[ORM\Entity()]
#[ORM\Table(name: 'kw_page__block')]
class Block extends AbstractBlock implements BlockInterface
{

}