<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\User;
use App\Form\Type\MarkDownType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditDepartmentType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $department = $this->entityManager->getRepository(Department::class)->findOneBy(['id' => $options['data']['departmentId']]);

        $builder
            ->add('departmentId', HiddenType::class)
            ->add('name', TextType::class, [
                'required' => true,
                'data' => $department->getName(),
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('managers', EntityType::class, [
                'class' => User::class,
                'attr' => array('class' => 'chzn-select'),
                'multiple' => true,
                'data' => $department->getManagers(),
                'placeholder' => 'Choose an option',
                'choice_label' => 'fullName',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Save",
            ])
            ->add('cancel', MarkDownType::class, [
                'label' => false,
                'data' => "<a href='" . $this->urlGenerator->generate('departments', [], true) . "' class='btn btn-primary'>Cancel</a>",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}