<?php

namespace App\Form\Type;

use App\Entity\Department;
use App\Entity\Holiday;
use App\Entity\Position;
use App\Validator\Constraints\EmailInUse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;


class HolidayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ]
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'placeholder' => 'Choose an option',
                'choice_label' => 'name',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('positions', EntityType::class, [
                'required' => true,
                'class' => Position::class,
                'placeholder' => 'Choose an option',
                'choice_label' => 'name',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('days', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),

                ]
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
//            'data_class' => Holiday::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}