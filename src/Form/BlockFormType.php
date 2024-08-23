<?php

namespace Kikwik\PageBundle\Form;

use Kikwik\PageBundle\Entity\Block;
use Kikwik\PageBundle\Service\BlockComponentProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlockFormType extends AbstractType
{
    public function __construct(
        private BlockComponentProvider $blockComponentProvider,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('component',BlockComponentChoiceType::class)
            ->add('isEnabled')
            ->add('parameters', FormType::class, [
                'compound' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();
            $form = $event->getForm();

            if (!$block || null === $block->getComponent()) {
                return;
            }

            $component  = $this->blockComponentProvider->getBlockComponent($block->getComponent());
            foreach ($component->getDefaultValues() as $field => $default)
            {
                $form->get('parameters')->add($field);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Block::class,
        ]);
    }
}