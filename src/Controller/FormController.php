<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FormController extends Controller
{
    /**
     * Try to access todos (will change the route when login is fine)
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/demo/form")
     * @Method({"GET"})
     */
    public function index() {
        return new Response('you are in');
    }

    /**
    * @Route("/demo/form/login", name="demo_login")
    */
    public function login(Request $request, AuthenticationUtils $authUtils, CsrfTokenManagerInterface $tokenManager)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        // token for csrf protection (no need to check validity from request coz it's up to Symfony to do this with
        // internal mecanisms
        $tokenParam = $this->getParameter('csrf_token_parameter');
        $tokenId = $this->getParameter('csrf_token_id');
        $token = $tokenManager->getToken($tokenId);

        return $this->render('form/login.html.twig', array(
            'last_username' => $lastUsername,
            'token'         => $token,
            'token_param'   => $tokenParam,
            'error'         => $error,
        ));
    }
}