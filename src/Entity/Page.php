<?php

namespace Kikwik\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kikwik\PageBundle\Model\PageInterface;

#[ORM\Entity()]
#[ORM\Table(name: 'kw_page__page')]
#[ORM\HasLifecycleCallbacks]
class Page extends AbstractPage implements PageInterface
{
    use \App\Entity\PageTrait;
}
