<?php

namespace Kikwik\PageBundle\Form;

use Kikwik\PageBundle\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,['label'=>'Internal name'])
            ->add('translations', CollectionType::class, [
                'entry_type' => PageTranslationFormType::class,
                'label'=>false,
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($view['translations']->children as $childView)
        {
            $childView->vars['label'] = $childView->vars['value']->getLocale();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'translation_domain' => 'kikwik_page',
        ]);
    }

}