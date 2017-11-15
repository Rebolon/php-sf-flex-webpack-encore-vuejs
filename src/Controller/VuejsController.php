<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class VuejsController extends Controller
{
    /**
     * @Route("/demo/vuejs")
     * @Method({"GET"})
     */
    public function index() {
        return $this->render('vuejs/app.html.twig');
    }

}