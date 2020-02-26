<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /**
     * @Cache(expires="+1 hour")
     * @Route(
     *     "/demo/hello/{name}",
     *     requirements={"name": "\w*"},
     *     defaults={"name": "world"},
     *     methods={"GET"}
     *     )
     *
     * @param $name
     *
     * @return Response
     */
    public function world($name)
    {
        return $this->render('hello/world.html.twig', ['name' => $name, ]);
    }
}
