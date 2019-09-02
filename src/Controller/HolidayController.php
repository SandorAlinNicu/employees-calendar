<?php

namespace App\Controller;

use App\Form\Type\HolidayType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Holiday;
use Symfony\Component\HttpFoundation\Request;

class HolidayController extends AbstractController
{
    /**
     * @Route("/holiday-request")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function holidayRequest(Request $request)
    {
        // just setup a fresh $task object (remove the example data)
        $holiday = new Holiday();

        $form = $this->createForm(HolidayType::class, $holiday);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$holiday` variable has also been updated
            $holiday = $form->getData();
        }

        return $this->render('pages/holiday-request.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}