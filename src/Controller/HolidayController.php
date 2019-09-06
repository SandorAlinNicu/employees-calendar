<?php

namespace App\Controller;

use App\Entity\Interval;
use App\Form\Type\HolidayType;
use DateTime;
use Knp\Snappy\Pdf;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Holiday;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    public function holidayRequest(Request $request, Security $security, FlashBagInterface $flashBag, Pdf $pdf, \Swift_Mailer $mailer): Response
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

            $intervals_array = [];
            $total_holiday_days = 0;
            foreach ($intervals as $key => $value) {

                $interval = new Interval();
                $interval->setFromDate($value['from']);
                $interval->setToDate($value['to']);
                if (empty($value['to'])) {
                    $interval->setToDate($value['from']);
                }
                $holiday->addInterval($interval);
                $total_holiday_days += $interval->getNumberOfDaysWithoutWeekend();
                if ($interval->getToDate()) {
                    $temp = "";
                    $temp = $interval->getFromDate()->format('Y-m-d H:i:s');
                    $temp .= ' -> ' . $interval->getToDate()->format('Y-m-d H:i:s');
                    $intervals_array[] = $temp;
                } else {
                    $intervals_array[] = $interval->getFromDate();
                }
            }
            //Snappy
            $pdf_name = 'requests/file' . time() . '.pdf';
            $pdf->generateFromHtml($this->renderView('email/pdfformat.html.twig', [
                'name' => $form->get('name')->getData(),
                'position' => $form->get('positions')->getData()->getName(),
                'number_of_days' => $total_holiday_days,
                'year' => date('Y', time()),
                'dates' => $intervals_array,
                'todaysDate' => time(),

            ]), $pdf_name);

            $temp = $holiday->getEmail();
            $email_sender = $_ENV['EMAIL_SENDING_ADDRESS'];
            $message = (new \Swift_Message('Holiday Notification ' . $holiday->getName()))
                ->setFrom($email_sender)
                ->setTo($temp)
                ->setBody(
                    $this->renderView('email/emailformat.html.twig', [
                        'number_of_days' => $total_holiday_days,
                        'dates' => $intervals_array,
                    ]),
                    'text/html'
                )
                ->attach(Swift_Attachment::fromPath($pdf_name));
            $mailer->send($message);

            //Snappy
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($holiday);
            $entityManager->flush();
            $flashBag->add('success', 'Holiday request was created, an email was sent on your address.');

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