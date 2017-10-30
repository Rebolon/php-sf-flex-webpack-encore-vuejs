<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class GhibliController extends Controller
{
    /**
     * @Route("/ghibli")
     */
    public function index() {
        return $this->render('ghibli/app.html.twig');
    }

}