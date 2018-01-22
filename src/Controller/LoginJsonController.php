<?php

namespace App\Controller;

use App\Security\CsrfToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LoginJsonController extends Controller
{
    /**
     * @Route("/demo/vuejs/login")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('spa.html.twig', ['appName' => 'login', 'useParent' => true, ]);
    }

    /**
     * New Json authentification system from Symfony 3.3
     * It relies on App\Security\ApiKeyAuthenticator for CSRF checks
     *
     * @Route("/demo/login/json", name="demo_login_json")
     *
     * @return JsonResponse
     */
    public function loginJson()
    {
        return new JsonResponse();
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
     *     "/demo/login/json/isloggedin",
     *     name="demo_secured_page_is_logged_in",
     *     )
     * @Method({"GET"})
     */
    public function isLoggedIn()
    {
        // will be usefull if we decide to return always 200 + the real Json content represented by isLoggedIn: 0|1
        $authenticated = $this->isGranted('IS_AUTHENTICATED_FULLY');
        $data = ['isLoggedIn' => (int)$authenticated, ];

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
