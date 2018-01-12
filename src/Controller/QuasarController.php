<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class QuasarController extends Controller
{
    /**
     * @Route("/demo/quasar")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('quasar/app.html.twig', ['appName' => 'quasar', ]);
    }
}
