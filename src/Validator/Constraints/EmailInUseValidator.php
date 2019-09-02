<?php


namespace App\Validator\Constraints;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintValidator;

class EmailInUseValidator extends ConstraintValidator
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * EmailInUseValidator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $value]);
        ;
        if ($user) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}