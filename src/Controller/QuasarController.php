<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class QuasarController extends Controller
{
    /**
     * @Route("/quasar")
     */
    public function index() {
        return $this->render('quasar/app.html.twig');
    }

}