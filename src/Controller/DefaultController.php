<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route(path="/")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $devServerStringFoundAtPos = strpos($request->server->get('SERVER_SOFTWARE'), 'Development Server');
        $isPhpBuiltInServer = false === $devServerStringFoundAtPos ? false : (bool) $devServerStringFoundAtPos;
        $hasPemCertificate = ini_get('curl.cainfo') && ini_get('openssl.cafile') ? true : false;

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
        $demoRoutes['api-platform: admin react'] = $router->generate('app_apiplatformadminreact_index');
        $demoRoutes['easy admin'] = $router->generate('admin');

        if (!$hasPemCertificate) {
            $demoRoutes['httpplug demo'] = [
                'uri' => $router->generate('app_httpplug_call'),
                'note' => 'You need to set php.ini vars: curl.cainfo and openssl.cafile to the path of the pem file.'
                    . ' if you need one, <a href="https://curl.haxx.se/docs/caextract.html">download the certificate</a>',
            ];
        }
        
        if ($isPhpBuiltInServer) {
            $note = 'You are using PHP Built-in server, api indexes for json/jsonld or html may not work and return a '
            . '404 Not Found';
            $demoRoutes['api-platform: rest'] = [
                'uri' => $demoRoutes['api-platform: rest'],
                'note' => $note,
                ];

            $demoRoutes['api-platform: admin react'] = [
                'uri' => $router->generate('app_apiplatformadminreact_index'),
                'note' => $note,
            ];
        }

        $render = $this->render(
            'default/menu.html.twig',
            [
            'routes' => $demoRoutes,
            'isPhpBuiltInServer' => $isPhpBuiltInServer,
            ]
        );

        return $render;
    }
}
