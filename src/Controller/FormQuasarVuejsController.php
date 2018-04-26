<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;

class FormQuasarVuejsController extends Controller
{
    /**
     * Try to access todos (will change the route when login is fine)
     *
     * @Route("/demo/form/quasar-vuejs")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('form-quasar-vuejs/app.html.twig', ['appName' => 'form-quasar-vuejs', ]);
    }
}
