<?php

namespace App\Controller;

use App\Security\UserInfo;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class LoginJsonController extends Controller
{
    /**
     * Try to test this security when the one on the bottom works Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/demo/security/login/json/secured", name="demo_secured_page_json")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function index()
    {
        $user = $this->getUser();

        return $this->render('login/index.html.twig', ['user' => $user, ]);
    }

    /**
     * The route that displays the JS form
     * @Route("/demo/security/login/json/frontend", name="demo_login_json")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function form()
    {
        return $this->render('spa-quasar.html.twig', ['appName' => 'login', 'useParent' => true, ]);
    }

    /**
     * New Json authentification system from Symfony 3.3
     * It relies on App\Security\ApiKeyAuthenticator for CSRF checks
     *
     * @Route("/demo/security/login/json/authenticate", name="demo_login_json_check")
     *
     * @var RouterInterface $router
     * @return Response
     */
    public function loginJson(RouterInterface $router)
    {
        $user = $this->getUser();

        if (!$user) {
            return new RedirectResponse($router->generate('demo_login_json'));
        }

        return $this->isLoggedIn();
    }

    /**
     * Try to test this security when the one on the bottom works Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * call it with .json extension and check if you have a 200
     *
     * @todo: should we let it as is, or always return a 200 and in the Json content set the isLoggedIn to 0 or 1 ?
     * For instance i stay on my first choice
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route(
     *     "/demo/security/login/json/isloggedin",
     *     name="demo_secured_page_json_is_logged_in",
     *     defaults={"_format"="json"}
     *     )
     * @Method({"GET", "POST"})
     *
     * @return Response
     */
    public function isLoggedIn()
    {
        $isGranted = function($att) {
            return $this->isGranted($att);
        };

        $getUser = function() {
            return $this->getUser();
        };

        return new JsonResponse(UserInfo::getUserInfo($isGranted, $getUser));
    }
}
