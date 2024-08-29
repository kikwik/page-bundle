<?php

namespace Kikwik\PageBundle\Form;

use Kikwik\PageBundle\Service\BlockComponentProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlockComponentChoiceType extends AbstractType
{
    public function __construct(private BlockComponentProvider $blockComponentProvider)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->blockComponentProvider->getBlockComponentChoices(),
            'placeholder'=>'',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'block_component_choice';
    }
}