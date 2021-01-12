<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security as SecurityComponent;

class LoginController extends AbstractController
{
    /**
     * Try to test this security when the one on the bottom works Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route(
     *     "/demo/security/login/standard/secured",
     *     name="demo_secured_page_standard",
     *     methods={"GET"}
     *     )
     */
    public function index()
    {
        $user = $this->getUser();

        return $this->render('login/index.html.twig', ['user' => $user, ]);
    }

    /**
     * Standard Symfony authentification system for a fronted in PHP
     *
     * @Route("/demo/security/login/standard", name="demo_login_standard")
     *
     * @param AuthenticationUtils $authUtils
     * @param CsrfTokenManagerInterface $tokenManager
     *
     * @param string $csrfTokenId
     * @return Response
     */
    public function loginStandard(
        AuthenticationUtils $authUtils,
        CsrfTokenManagerInterface $tokenManager,
        string $csrfTokenId
    ) {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        // token for csrf protection (no need to check validity from request coz it's up to Symfony to do this with
        // internal mecanisms)
        $token = $tokenManager->getToken($csrfTokenId);

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'token'         => $token,
            'error'         => $error,
        ]);
    }

    /**
     * This action is the same as LoginJsonController, that's why i extracted quickly the content into a static method (even if a part of developers don't like that)
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route(
     *     "/demo/security/login/standard/isloggedin",
     *     name="demo_secured_page_standard_is_logged_in",
     *     defaults={"_format"="json"},
     *     methods={"GET", "POST"}
     * )
     */
    public function isLoggedIn(TokenStorageInterface $token, SecurityComponent $security)
    {
        $isAuthenticated = $security->isGranted('IS_AUTHENTICATED_FULLY');
        $data = ['isLoggedIn' => (int)$isAuthenticated];

        if ($isAuthenticated) {
            $user = $token->getToken()->getUser();
            $data['me'] = [
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ];
        }

        return new JsonResponse($data);
    }
}
