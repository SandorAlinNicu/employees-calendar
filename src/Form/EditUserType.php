<?php


namespace App\Form;


use App\Entity\Department;
use App\Entity\Position;
use App\Form\Type\MarkDownType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EditUserType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userId', HiddenType::class)
            ->add('fullName', TextType::class, [
                'label' => 'Full Name',
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => "/^[A-Za-z][A-Za-z\'\-]+([\ A-Za-z][A-Za-z\'\-]+)*/",
                        'match' => true,
                        'message' => "Your name should contain only characters or special characters like -/'",
                    ])
                ]
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'placeholder' => 'Choose an option',
                'choice_label' => 'name',
            ])
            ->add('position', EntityType::class, [
                'class' => Position::class,
                'placeholder' => 'Choose an option',
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class)
            ->add('cancel', MarkDownType::class, [
                'label' => false,
                'data' => "<a href='" . $this->urlGenerator->generate('users', [], true) . "' class='btn btn-primary'>Cancel</a>",
            ]);
    }
}