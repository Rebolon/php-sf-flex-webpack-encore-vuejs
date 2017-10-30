<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends Controller
{
    /**
     * @Route("/hello/world")
     */
    public function world() {
        return $this->render('hello/world.html.twig', ['name' => 'world', ]);
    }

}