<?php

namespace Kikwik\PageBundle\Form;

use Kikwik\PageBundle\Entity\PageTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class PageTranslationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('isEnabled')
            ->add('blocks', LiveCollectionType::class, [
                'entry_type'=>BlockFormType::class,
                'label'=>false,
                'button_delete_options' => [
                    'label' => 'X',
                    'attr' => [
                        'class' => 'btn btn-outline-danger',
                    ],
                ],
                'button_add_options' => [
                    'label' => '+',
                    'attr' => [
                        'class' => 'btn btn-outline-primary',
                    ],
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PageTranslation::class,
        ]);
    }
}