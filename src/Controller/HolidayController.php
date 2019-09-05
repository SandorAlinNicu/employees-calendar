<?php

namespace App\Controller;

use App\Form\Type\HolidayType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Holiday;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


class HolidayController extends AbstractController
{
    /**
     * @Route("/holiday-request")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function holidayRequest(Request $request, Security $security): Response
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
            $holiday = $form->getData();
        }

        return $this->render('pages/form_page.html.twig', [
            'form' => $form->createView(),
            'title' => 'Request Holiday',
        ]);
    }
}