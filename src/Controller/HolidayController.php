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

        $form = $this->createForm(HolidayType::class, [
            'email' => $user->getUsername()
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $holiday = $form->getData();
        }

        return $this->render('pages/holiday-request.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}