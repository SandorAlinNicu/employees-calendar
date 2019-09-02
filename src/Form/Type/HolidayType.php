<?php

namespace App\Form\Type;

use App\Entity\Holiday;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class HolidayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('departments', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    '' => '',
                    'PHP' => 'php_department',
                    'JS' => 'js_department',
                    '.NET' => '.net_department'
                ]])
            ->add('positions', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    '' => '',
                    'Developers' => 'position_developers',
                    'Junior Developer' => 'position_junior',
                    'Manager' => 'position_manager',
                    'HR Manager' => 'position_hrmanager'
                ]])
            ->add('days', NumberType::class, [
                'required' => true
            ])
            ->add('save', SubmitType::class);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Holiday::class,
        ]);
    }
}