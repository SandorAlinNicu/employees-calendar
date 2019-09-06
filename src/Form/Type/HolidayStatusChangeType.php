<?php

namespace App\Form\Type;

use App\Form\Type\MarkDownType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HolidayStatusChangeType extends AbstractType
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
            ->add('holidayId', HiddenType::class)
            ->add('submit', SubmitType::class, [
                'label' => "Yes",
            ])
            ->add('cancel', MarkDownType::class, [
                'label' => false,
                'data' => "<a href='" . $this->urlGenerator->generate('requests', [], true) . "' class='btn btn-primary'>No</a>",
            ]);
    }
}