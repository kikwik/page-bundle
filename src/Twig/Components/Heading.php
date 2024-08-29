<?php

namespace Kikwik\PageBundle\Twig\Components;

use Kikwik\PageBundle\Block\BaseBlockComponent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

class Heading extends BaseBlockComponent
{
    public function getDefaultValues(): array
    {
        return [
            'tag'=>'h1',
            'title'=>'Title',
        ];
    }

    public function buildEditForm(FormInterface $form): void
    {
        $form
            ->add('tag',ChoiceType::class,[
                'choices'=>['h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4','h5'=>'h5','h6'=>'h6'],
            ])
            ->add('title',TextType::class);
    }


}