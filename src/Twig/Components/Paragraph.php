<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Block\BaseBlockComponent;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;

class Paragraph extends BaseBlockComponent
{
    public function getDefaultValues(): array
    {
        return [
            'content'=>'text placeholder',
        ];
    }

    public function buildEditForm(FormInterface $form): void
    {
        $form
            ->add('content', TextareaType::class, [])
            ;
    }


}