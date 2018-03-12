<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DevXpressAngularController extends Controller
{
    /**
     * @Route("/demo/devxpress-angular")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('devxpress-angular/app.html.twig', ['appName' => 'devxpress-angular', ]);
    }
}
