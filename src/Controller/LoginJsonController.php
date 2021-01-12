<?php

namespace App\Controller;

use App\Security\UserInfo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginJsonController extends AbstractController
{
    /**
     * Try to test this security when the one on the bottom works Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route(
     *     "/demo/security/login/json/secured",
     *     name="demo_secured_page_json",
     *     methods={"GET"}
     *     )
     * @Cache(maxage="2 weeks")
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
     * @Route(
     *     "/demo/security/login/json/frontend",
     *     name="demo_login_json",
     *     methods={"GET"}
     *     )
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
     * @Route(
     *     "/demo/security/login/json/authenticate",
     *     name="demo_login_json_check"
     *     )
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
     *     defaults={"_format"="json"},
     *     methods={"GET", "POST"}
     * )
     *
     * @return Response
     */
    public function isLoggedIn()
    {
        // will be usefull if we decide to return always 200 + the real Json content represented by isLoggedIn: 0|1
        $authenticated = $this->isGranted('IS_AUTHENTICATED_FULLY');
        $data = ['isLoggedIn' => (int)$authenticated,];

        if ($authenticated) {
            $user = $this->getUser();
            $data['me'] = [
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ];
        }

        return new JsonResponse($data);
    }
}
