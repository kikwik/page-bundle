<?php

namespace Kikwik\PageBundle\Form;

use Kikwik\PageBundle\Entity\PageTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class PageTranslationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isEnabled',null,['label'=>'Translation enabled'])
            ->add('title')
            ->add('description')
        ;

        // Add an event listener to add 'blocks' field only for new entities
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // Check if the entity is new (i.e., has no ID yet)
            if ($data instanceof PageTranslation && $data->getId() !== null) {
                $form->add('blocks', LiveCollectionType::class, [
                    'entry_type' => BlockFormType::class,
                    'label' => false,
                    'button_delete_options' => [
                        'label' => 'X remove block',
                        'attr' => [
                            'class' => 'btn btn-outline-danger',
                        ],
                    ],
                    'button_add_options' => [
                        'label' => '+ add block',
                        'attr' => [
                            'class' => 'btn btn-outline-primary',
                        ],
                    ],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PageTranslation::class,
            'translation_domain' => 'kikwik_page',
        ]);
    }
}