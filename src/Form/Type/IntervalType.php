<?php

namespace App\Form\Type;

use App\Entity\Department;
use App\Entity\Interval;
use App\Entity\Position;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class IntervalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('from', DateType::class, [
                'widget' => 'single_text',
                'placeholder' => 'Choose your start date',
                'format' => 'dd-MM-yyyy',
                'label' => 'Start date',
                'attr' => [
                    'class' => 'datepicker',
                    'autocomplete' => 'off'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('to', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'datepicker',
                    'autocomplete' => 'off'
                ],
                'label' => 'Finish date',
            ]);
        //  ->add('add new field');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => Interval::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}