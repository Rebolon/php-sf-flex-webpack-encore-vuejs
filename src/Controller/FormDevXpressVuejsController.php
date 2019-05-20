<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FormDevXpressVuejsController extends AbstractController
{
    /**
     * @Route(
     *     "/demo/form/devxpress-vuejs/{routeName}",
     *     requirements={"routeName"=".*"},
     *     defaults={"routeName"="home"}, methods={"GET"}
     * )
     */
    public function index()
    {
        return $this->render('form-devxpress-vuejs/app.html.twig', ['appName' => 'form-devxpress-vuejs', 'title' => 'DevxpressVueJS', ]);
    }
}
