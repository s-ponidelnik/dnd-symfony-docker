<?php

namespace App\Form;

use App\Entity\SpellSchool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpellSchool2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('nameEn')
            ->add('nameRu')
            ->add('descriptionEn')
            ->add('descriptionRu')
            ->add('source')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SpellSchool::class,
        ]);
    }
}
