<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AddAdminUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'add-admin-user';
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    private $em;

    public function __construct(bool $requirePassword = false, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->requirePassword = $requirePassword;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
        parent::__construct();
    }

    protected function configure()
    {
        // Console inputs
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $outputMessage = '';
        $userRepository = $this->em->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(array('username' => $input->getArgument('email')));
        if (isset($existingUser)) {
            $existingRoles = $existingUser->getRoles();
            if (!in_array('ROLE_ADMIN', $existingRoles)) {
                $existingRoles[] = 'ROLE_ADMIN';
                $existingUser->setRoles($existingRoles);
                $outputMessage .= 'This user now has the admin role.';
            } else {
                $outputMessage .= 'This user already has the admin role.';
            }

            $passwordConstraint = "The password must be grater than 6 characters.";
            $passwordLength = strlen($input->getArgument('password'));
            if ($passwordLength < 6) {
                $output->writeln($passwordConstraint);
                return;
            }

            $existingUser->setPassword(
                $this->passwordEncoder->encodePassword(
                    $existingUser,
                    $input->getArgument('password')
                )
            );
            $existingUser->setActive(true);

            $this->em->persist($existingUser);
            $this->em->flush();
            $output->writeln($outputMessage);

        } else {

            // Create the user object
            $user = new User();

            // Validate the email
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = 'Invalid email address';
            $email = $input->getArgument('email');
            $errors = $this->validator->validate(
                $email,
                $emailConstraint
            );
            if ($errors->count() || empty($email)) {
                $output->writeln($emailConstraint->message);
                return;
            }

            // Validate the password (min 6 chars)
            $passwordConstraint = "The password must be grater than 6 characters.";
            $passwordLength = strlen($input->getArgument('password'));
            if ($passwordLength < 6) {
                $output->writeln($passwordConstraint);
                return;
            }

            // Set the user's proprieties
            $user->setUsername($input->getArgument('email'));
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $input->getArgument('password')
                )
            );
            $user->setRoles(array('ROLE_ADMIN'));
            $user->setEmail($input->getArgument('email'));
            $user->setActive(true);

            // Persist and flash in database
            $this->em->persist($user);
            $this->em->flush();

            // Console outputs
            $output->writeln([
                '',
                '                                          Admin created:',
                '==========================================================================================================',
                '',
            ]);
            $output->writeln('                                    Username: ' . $input->getArgument('email'));
            $output->writeln([
                '',
                '==========================================================================================================',
            ]);
        }
    }
}