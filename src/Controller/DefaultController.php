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
        $router = $this->get('router');

        $demoRoutes['simple controller'] = $router->generate('simple');
        $demoRoutes['hello controller with twig'] = $router->generate('app_hello_world', ['name' => 'world', ]);
        $demoRoutes['httpplug demo'] = $router->generate('app_httpplug_call');

        $demoRoutes['symfony secured page with standard login'] = $router->generate('demo_secured_page');
        $demoRoutes['vuejs secured page with json login'] = $router->generate('app_loginjson_index');

        $demoRoutes['vuejs page with vue-router'] = $router->generate('app_vuejs_index');
        $demoRoutes['vuejs with quasar and vue-router'] = $router->generate('app_quasar_index');
        $demoRoutes['vuejs with quasar with a more complex app'] = $router->generate('app_form_index');

        $demoRoutes['csrf token generation'] = $router->generate('token');
        $demoRoutes['user login check for js app'] = $router->generate('demo_secured_page_is_logged_in');

        $demoRoutes['api-platform: rest'] = $router->generate('api_entrypoint');
        $demoRoutes['api-platform: graphql'] = $router->generate('api_graphql_entrypoint');
        $demoRoutes['easy admin'] = $router->generate('admin');

        $render = $this->render('default/menu.html.twig', ['routes' => $demoRoutes,]);

        return $render;
    }

}
