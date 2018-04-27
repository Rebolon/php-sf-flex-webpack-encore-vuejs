<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FormDevXpressAngularController extends Controller
{
    /**
     * @Route("/demo/form/devxpress-angular/{ngRouteName}", requirements={"ngRouteName"=".*"}, defaults={"ngRouteName"="home"})
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('form-devxpress-angular/app.html.twig', ['appName' => 'devxpress-angular', 'title' => 'DevxpressAngular', ]);
    }
}
