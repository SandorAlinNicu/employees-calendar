<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class HomepageController extends BasicController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('pages/basic_page.html.twig', [
            'title' => 'Homepage'
        ]);
    }
}