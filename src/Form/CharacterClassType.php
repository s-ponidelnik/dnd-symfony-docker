<?php

namespace App\Form;

use App\Entity\CharacterClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CharacterClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier')
            ->add('nameEn')
            ->add('nameRu')
            ->add('descriptionEn')
            ->add('descriptionRu')
            ->add('source')
            ->add('spells')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CharacterClass::class,
        ]);
    }
}
