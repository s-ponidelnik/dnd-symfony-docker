<?php

namespace App\Form;

use App\Entity\Spell;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Spell4Type extends AbstractType
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
            ->add('casting_time_type')
            ->add('casting_time_description_ru')
            ->add('casting_time_description_en')
            ->add('duration_type')
            ->add('duration')
            ->add('areaType')
            ->add('areaSize')
            ->add('rangeType')
            ->add('areaSizeType')
            ->add('school')
            ->add('characterClasses')
            ->add('sources')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Spell::class,
        ]);
    }
}
