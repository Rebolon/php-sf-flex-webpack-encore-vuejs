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
     * @Route("/demo/login/secured")
     * @Method({"GET"})
     */
    public function index() {
        return new Response('you are in');
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

    /**
     * New Json authentification system from Symfony 3.3
     * it will return a {error: {text|{code: "", "message": "": "exception: []}} or what you want from your own controller
     *
     * @Route("/demo/login/json", name="demo_login_json")
     */
    public function loginJson(Request $request, CsrfToken $csrfTokenManager)
    {
        try {
            $tokenId = $this->getParameter('csrf_token_id');
            $tokenKey = "csrf";
            $content = $request->getContent();
            $contentJson = json_decode($content, true);
            if (!is_array($contentJson) || !array_key_exists($tokenKey, $contentJson)) {
                throw new \InvalidArgumentException("Token mandatory");
            }
            $csrfTokenManager->tokenCheck($tokenId, $contentJson[$tokenKey]);

            return new JsonResponse();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => ["code" => 400, "message" => $e->getMessage(), "exception" => $e, ], ]);
        }
    }
}