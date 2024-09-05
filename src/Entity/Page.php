<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Kikwik\PageBundle\Model\PageInterface;

#[ORM\Entity()]
#[Table(name: 'kw_page__page')]
class Page extends AbstractPage implements PageInterface
{


}
