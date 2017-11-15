<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route(path="/")
     * @return Response
     */
    public function index() {
        $routes = $this->get('router')->getRouteCollection()->all();

        $demoRoutes = [];
        foreach ($routes as $routeName => $route) {
            if (strpos($route->getPath(), '/demo/') !== false
                && in_array('GET', $route->getMethods())) {
                $demoRoutes[$routeName] = $route;
            }
        }

        $render = $this->render('default/menu.html.twig', ['routes' => $demoRoutes,]);

        return $render;
    }

}