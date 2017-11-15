<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class VuejsController extends Controller
{
    /**
     * @Route("/vuejs")
     */
    public function index() {
        return $this->render('vuejs/app.html.twig');
    }

}