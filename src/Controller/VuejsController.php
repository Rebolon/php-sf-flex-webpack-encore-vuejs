<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VuejsController extends AbstractController
{
    /**
     * @Route("/demo/vuejs", methods={"GET"})
     */
    public function index()
    {
        return $this->render('vuejs/app.html.twig', ['appName' => 'vuejs', 'useParent' => true, ]);
    }
}
