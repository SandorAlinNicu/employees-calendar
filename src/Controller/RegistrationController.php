<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends BasicController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(FlashBagInterface $flashBag, Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenStorageInterface $tokenStorage, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, \Swift_Mailer $mailer): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setUsername($form->get('email')->getData());
            $user->setFullName($form->get('fullName')->getData());
            $user->setActive(false);
            $user->setActivationToken(hash('md5', $user->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $email_sender = $_ENV['EMAIL_SENDING_ADDRESS'];
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom($email_sender)
                ->setTo($user->getEmail())
                ->setBody(
                    $this->render('email/registrationconfirmation.html.twig', [
                        'urlpath' => $this->generateUrl('activate_user', ['token' => $user->getActivationToken()], UrlGeneratorInterface::ABSOLUTE_URL),
                    ]),
                    'text/html'
                );
            $mailer->send($message);
            $flashBag->add('warning', 'An email was sent on your address with an activation link.');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('pages/centered_form_page.html.twig', [
            'form' => $form->createView(),
            'title' => 'Register',
        ]);
    }

    /**
     * @Route("/user/activation/{token}", name="activate_user")
     *
     */
    public function confirmationRegister($token)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }
        // Find user with provided token.
        $entityManager = $this->getDoctrine()->getManager();
        $userRepo = $entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['activationToken' => $token]);
        if ($user == null) {
            $this->addFlash('danger', 'This activation token is already used or is invalid!');
            return $this->redirectToRoute('homepage');
        }
        $user->setActive(true);
        $user->deleteActivationToken();
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Your account is successfully created!');
        return $this->redirectToRoute('homepage');
    }

}
