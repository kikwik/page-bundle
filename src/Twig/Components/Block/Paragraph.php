<?php

namespace Kikwik\PageBundle\Twig\Components\Block;

use Kikwik\PageBundle\Block\BaseBlockComponent;

class Paragraph extends BaseBlockComponent
{
    public function getDefaultValues(): array
    {
        return [
            'content'=>'text placeholder',
        ];
    }


}