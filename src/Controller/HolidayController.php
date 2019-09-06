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

    /**
     * @Route("/calendar", name="calendar")
     */
    public function requests(Security $security)
    {
        $user = $security->getUser();
        $users = array($user);
        if (count(array_intersect($user->getRoles(), array('ROLE_ADMIN', 'ROLE_MANAGER')))) {
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        }
        $requests = $this->getDoctrine()->getRepository(Holiday::class)->findAll();
        $grouped_requests = [];
        /** @var Holiday $request */
        foreach ($requests as $request) {
            $request_days = [];
            foreach ($request->getIntervals() as $interval) {
                $days = $interval->getDaysArrayInCurrentMonth(date('Y-m-d'));
                if (!empty($days)) {
                    $request_days = array_merge($request_days, $days);
                }

            }
            if (!isset($grouped_requests[$request->getEmail()])) {
                $grouped_requests[$request->getEmail()] = [];
            }
            $grouped_requests[$request->getEmail()] = array_unique(array_merge($grouped_requests[$request->getEmail()], $request_days));

        }
        return $this->render('calendar.html.twig', [
            'grouped_requests' => $grouped_requests,
            'users' => $users,
            'max_days' => date('t'),
            'day' => date('j'),
            'month' => date('F'),
            'year' => date('Y'),
            'title' => 'Calendar'
        ]);
    }
}