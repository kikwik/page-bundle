<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kikwik\PageBundle\Model\PageInterface;
use Kikwik\PageBundle\Repository\PageRepository;

#[ORM\Entity()]
#[ORM\Table(name: 'kw_page__page')]
class Page extends AbstractPage implements PageInterface
{
}
