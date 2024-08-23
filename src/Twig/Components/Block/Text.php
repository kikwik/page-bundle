<?php

namespace Kikwik\PageBundle\Twig\Components\Block;

use Kikwik\PageBundle\Block\BaseBlockComponent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Text extends BaseBlockComponent
{
    public function getDefaultValues(): array
    {
        return [
            'content'=>'text placeholder',
        ];
    }


}