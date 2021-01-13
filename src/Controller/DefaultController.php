<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route(
     *     path="/",
     *     methods={"GET"}
     *     )
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $devServerStringFoundAtPos = strpos($request->server->get('SERVER_SOFTWARE'), 'Development Server');
        $isPhpBuiltInServer = false === $devServerStringFoundAtPos ? false : (bool) $devServerStringFoundAtPos;
        $hasPemCertificate = ini_get('curl.cainfo') && ini_get('openssl.cafile') ? true : false;

        $router = $this->get('router');

        $demoRoutes = [];
        $demoRoutes['Basic: Simple controller'] = $router->generate('simple');
        $demoRoutes['Basic: Hello controller with twig'] = $router->generate('app_hello_world', ['name' => 'world', ]);
        $demoRoutes['Basic: HttpClient demo'] = $router->generate('app_http_call');

        $demoRoutes['Login: Symfony secured page with form_login (statefull)'] = $router->generate('demo_secured_page_standard');
        $demoRoutes['Login: Vuejs secured page with json_login (statefull)'] = $router->generate('demo_login_json_check'); // if i go to demo_secured_page_json i will just get a json string !!! user won't know how to go to the form uri (i may add the uri in the response, but if i link to the form and the user is already logged, it will then be redirected to the secured page)
        $demoRoutes['Login: Quasar secured page with JWT system (stateless)'] = $router->generate('demo_login_jwt');

        $demoRoutes['JS app: Csrf token generation for statefull app'] = $router->generate('token');
        $demoRoutes['JS app: User login check (statefull)'] = $router->generate('demo_secured_page_json_is_logged_in');

        $demoRoutes['Vuejs: page with vue-router'] = $router->generate('vuejs');
        $demoRoutes['Vuejs: with quasar and vue-router'] = $router->generate('vuejs_quasar');

        $demoRoutes['Form & grid: Quasar with Vuejs'] = $router->generate('vuejs_form_quasar');
        $demoRoutes['Form & grid: DevXpress with Vuejs'] = $router->generate('vuejs_form_devxpress');
        $demoRoutes['Form & grid: DevXpress with Angular11'] = $router->generate('app_formdevxpressangular_index');

        $demoRoutes['Api-platform: rest'] = $router->generate('api_entrypoint');
        $demoRoutes['Api-platform: graphql'] = $router->generate('api_graphql_entrypoint');
        $demoRoutes['Api-platform: admin react'] = $router->generate('admin');
        $demoRoutes['Easy admin'] = $router->generate('easyadmin');

        if (!$hasPemCertificate) {
            $demoRoutes['Basic: HttpClient demo'] = [
                'uri' => $demoRoutes['Basic: HttpClient demo'],
                'note' => 'You need to set php.ini vars: curl.cainfo and openssl.cafile to the path of the pem file.'
                    . ' if you need one, <a href="https://curl.haxx.se/docs/caextract.html">download the certificate</a>',
            ];
        }

        if ($isPhpBuiltInServer) {
            $note = 'You are using PHP Built-in server, api indexes for json/jsonld or html may not work and return a '
            . '404 Not Found';

            $demoRoutes['Form & grid: Quasar with Vuejs'] = [
                'uri' => $demoRoutes['Form & grid: Quasar with Vuejs'],
                'note' => 'You are using PHP Built-in server, api indexes for json/jsonld or html may not work and return a '
                    . '404 Not Found',
            ];

            $demoRoutes['Api-platform: rest'] = [
                'uri' => $demoRoutes['Api-platform: rest'],
                'note' => $note,
                ];

            $demoRoutes['Api-platform: admin react'] = [
                'uri' => $router->generate('admin'),
                'note' => $note,
            ];
        }

        return $this->render(
            'default/menu.html.twig',
            [
            'routes' => $demoRoutes,
            'isPhpBuiltInServer' => $isPhpBuiltInServer,
            ]
        );
    }
}
