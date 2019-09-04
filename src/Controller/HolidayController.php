<?php

namespace App\Controller;

use App\Entity\Interval;
use App\Form\Type\HolidayType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Holiday;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Date;


class HolidayController extends BasicController
{
    /**
     * @Route("/holiday-request", name="holiday-request")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function holidayRequest(Request $request, Security $security, FlashBagInterface $flashBag): Response
    {

        $user = $security->getUser();

        if (isset($user)) {
            $username = $user->getUsername();
        } else {
            $username = '';
        }

        $form = $this->createForm(HolidayType::class, [
            'email' => $username,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $holiday = new Holiday();
            $holiday->setName($form->get('name')->getData());
            $holiday->setEmail($form->get('email')->getData());
            $holiday->setDepartment($form->get('department')->getData());
            $holiday->setPosition($form->get('positions')->getData());

            $intervals = $form->get('intervals')->getData();

            foreach ($intervals as $key => $value) {

                $interval = new Interval();
                $interval->setFromDate($value['from']);
                $interval->setToDate($value['to']);
                $holiday->addInterval($interval);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($holiday);
            $entityManager->flush();
            $flashBag->add('success', 'Holiday request was created.');
            return $this->redirectToRoute("homepage");
        }

        return $this->render('pages/form_page.html.twig', [
            'form' => $form->createView(),
            'title' => 'Request Holiday',
        ]);
    }
}