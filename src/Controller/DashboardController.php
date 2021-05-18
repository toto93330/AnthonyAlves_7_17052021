<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {


        $userapikey = "qsdqshjihgzrihhczcbqsqgs";


        return $this->render('dashboard/index.html.twig', [
            'user_api_key' => $userapikey,
        ]);
    }
}
