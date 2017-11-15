<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends Controller
{
    /**
     * @Cache(expires="+1 hour")
     * @Route("/hello/{name}", requirements={"name": "\w*"})
     */
    public function world($name) {
        return $this->render('hello/world.html.twig', ['name' => $name,]);
    }

}