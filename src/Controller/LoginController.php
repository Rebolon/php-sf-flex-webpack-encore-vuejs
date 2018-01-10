<?php

namespace App\Controller;

use App\Security\CsrfToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{
    /**
     * Try to test this security when the one on the bottom works Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/demo/login/secured", name="demo_secured_page")
     * @Method({"GET"})
     */
    public function index() {
        $user = $this->getUser();

        return $this->render('login/index.html.twig', ['user' => $user, ]);
    }

    /**
     * Standard Symfony authentification system for a fronted in PHP
     *
     * @Route("/demo/login/standard", name="demo_login_standard")
     */
    public function loginStandard(Request $request, AuthenticationUtils $authUtils, CsrfTokenManagerInterface $tokenManager)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        // token for csrf protection (no need to check validity from request coz it's up to Symfony to do this with
        // internal mecanisms
        $tokenId = $this->getParameter('csrf_token_id');
        $token = $tokenManager->getToken($tokenId);

        return $this->render('login/login.html.twig', array(
            'last_username' => $lastUsername,
            'token'         => $token,
            'error'         => $error,
        ));
    }
}
