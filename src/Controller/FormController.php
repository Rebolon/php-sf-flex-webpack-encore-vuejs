<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
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
    public function login(Request $request, AuthenticationUtils $authUtils, CsrfTokenManagerInterface $tokenManager)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        /** my own code for csrf coz crsf_token() twig helper doesn't exists in my stack, why ??? **/
        /* don't use container, send params by DI */
        // $tokenManager = $this->get('security.csrf.token_manager');
        $tokenId = $this->getParameter('csrf_token_id');
        $token = $tokenManager->getToken($tokenId);

        return $this->render('form/login.html.twig', array(
            'last_username' => $lastUsername,
            'token'         => $token,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/token", name="token")
     * @param Request $request
     * @param ParameterBag $params
     * @return JsonResponse
     */
    public function token(Request $request, ParameterBag $params) {
        $tokenId = $params->get('csrf_token_id');
        $tokenValue = $request->get('token');
        $token = new CsrfToken($tokenId, $tokenValue);

        return new JsonResponse(['token' => $token->getValue(), ]);
    }

    /**
     * @Route("/token_check", name="token")
     * @param Request $request
     * @param CsrfTokenManagerInterface $tokenManager
     * @param Parameter $params
     * @return Response
     */
    public function tokenCheck(Request $request, CsrfTokenManagerInterface $tokenManager, ParameterBag $params) {
        // @todo use the token action, decode the json and use it here instead of doing the same thing
        $tokenId = $params->get('csrf_token_id');
        $tokenValue = $request->get('token');
        $token = new CsrfToken($tokenId, $tokenValue);

        if (!$tokenManager->isTokenValid($token)) {
            throw new HttpException(400, 'Invalid token');
        }

        return new Response();
    }
}