<?php

namespace Kikwik\PageBundle\Form;

use Kikwik\PageBundle\Entity\Block;
use Kikwik\PageBundle\Service\BlockComponentProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
            ->add('position')
            ->add('isEnabled',null,['label'=>'Block enabled'])
            ->add('component',BlockComponentChoiceType::class)
            ->add('parameters', FormType::class, [
                'compound' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->updateParametersForm($event);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $this->updateParametersForm($event);
        });
    }

    private function updateParametersForm(FormEvent $event): void
    {
        $data = $event->getData();
        if (is_null($data)) {
            return;
        }
        $form = $event->getForm();

        $componentName = is_object($data) ? $data->getComponent() : ($data['component'] ?? null);
        if ($componentName)
        {
            $blockComponent = $this->blockComponentProvider->getBlockComponent($componentName);
            if ($blockComponent)
            {
                $parametersForm = $form->get('parameters');
                foreach ($parametersForm->all() as $fieldName => $child) {
                    $parametersForm->remove($fieldName);
                }
                $blockComponent->buildEditForm($parametersForm);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Block::class,
            'translation_domain' => 'kikwik_page',
        ]);
    }
}