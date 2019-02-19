<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FormQuasarVuejsController extends AbstractController
{
    /**
     * Try to access todos (will change the route when login is fine)
     *
     * @Route(
     *     "/demo/form/quasar-vuejs",
     *     methods={"GET"}
     *     )
     */
    public function index()
    {
        return $this->render('form-quasar-vuejs/app.html.twig', ['appName' => 'form-quasar-vuejs', ]);
    }
}
