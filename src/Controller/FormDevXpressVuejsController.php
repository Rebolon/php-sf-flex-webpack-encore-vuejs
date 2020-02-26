<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
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
     * @Cache(maxage="2 weeks")
     */
    public function index()
    {
        return $this->render('form-devxpress-vuejs/app.html.twig', ['appName' => 'form-devxpress-vuejs', 'title' => 'DevxpressVueJS', ]);
    }
}
