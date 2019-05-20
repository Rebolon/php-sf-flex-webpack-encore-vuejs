<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuasarController extends AbstractController
{
    /**
     * @Route("/demo/quasar", methods={"GET"})
     */
    public function index()
    {
        return $this->render('quasar/app.html.twig', ['appName' => 'quasar', ]);
    }
}
