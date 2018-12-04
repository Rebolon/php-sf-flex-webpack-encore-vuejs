<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FormDevXpressVuejsController extends Controller
{
    /**
     * @Route("/demo/form/devxpress-vuejs/{routeName}", requirements={"routeName"=".*"}, defaults={"routeName"="home"})
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('form-devxpress-vuejs/app.html.twig', ['appName' => 'form-devxpress-vuejs', 'title' => 'DevxpressVueJS', ]);
    }
}
