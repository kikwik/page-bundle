<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Block\BaseBlockComponent;

class Heading extends BaseBlockComponent
{
    public function getDefaultValues(): array
    {
        return [
            'tag'=>'h1',
            'content'=>'Title',
        ];
    }

}