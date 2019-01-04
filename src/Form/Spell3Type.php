<?php

namespace App\Form;

use App\Entity\Spell;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Spell3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier')
            ->add('level')
            ->add('casting_time')
            ->add('is_ritual')
            ->add('concentration')
            ->add('range_distance')
            ->add('verbal_component')
            ->add('somatic_component')
            ->add('material_components')
            ->add('nameEn')
            ->add('nameRu')
            ->add('descriptionEn')
            ->add('descriptionRu')
            ->add('school')
            ->add('source')
            ->add('characterClasses')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Spell::class,
        ]);
    }
}
