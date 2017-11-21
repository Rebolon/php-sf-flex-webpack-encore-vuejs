<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FormController extends Controller
{
    /**
     * Try to test this security when the one on the bottom works Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/demo/form/secured")
     * @Method({"GET"})
     */
    public function index() {
        return new Response('you are in');
    }

    /**
    * @Route("/login", name="login")
    */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        /** my own code for csrf coz crsf_token() twig helper doesn't exists in my stack, why ??? **/
        /* don't use container, send params by DI */
        $tokenManager = $this->get('security.csrf.token_manager');
        $inception = $this->container->getParameter('crsf_inscription_inception');
        $token = $tokenManager->getToken($inception);

        return $this->render('form/login.html.twig', array(
            'last_username' => $lastUsername,
            'token'         => $token,
            'error'         => $error,
        ));
    }

    public function checkToken(Request $request,) {
        // must be passed as method params
        $tokenManager = $this->get('security.csrf.token_manager');
        // must be passed as method param
        $tokenId = $this->container->getParameter('crsf_inscription_inception');
        $tokenValue = $request->get('token');
        $token = new CsrfToken($tokenId, $tokenValue);
        if (!$tokenManager->isTokenValid($token)) {
            throw new HttpException(400, 'Invalid token');
        }

        return true;
    }
}