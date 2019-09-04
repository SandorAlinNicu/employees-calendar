<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntervalCollectionType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entry_type' => IntervalType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => array(
                'label' => false
            )
        ]); // TODO: Change the autogenerated stub
    }

    public function getParent()
    {
        return CollectionType::class; // TODO: Change the autogenerated stub
    }
}