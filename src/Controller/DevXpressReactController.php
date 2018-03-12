<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DevXpressReactController extends Controller
{
    /**
     * @Route("/demo/devxpress-react")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('devxpress-react/app.html.twig', ['appName' => 'devxpress-react', 'useParent' => true, ]);
    }
}
