<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends Controller
{
    /**
     * @Cache(expires="+1 hour")
     * @Route("/demo/hello/{name}", requirements={"name": "\w*"}, defaults={"name": "world"})
     * @Method({"GET"})
     *
     * @param $name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function world($name)
    {
        return $this->render('hello/world.html.twig', ['name' => $name,]);
    }
}
