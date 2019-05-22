<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class LoginJwtController extends AbstractController
{
    /**
     * Try to test this security when the one on the bottom works Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route(
     *     "/demo/security/login/jwt/secured",
     *     name="demo_secured_page_jwt",
     *     methods={"GET"})
     */
    public function index()
    {
        $user = $this->getUser();

        return $this->render('login/index.html.twig', ['user' => $user, ]);
    }

    /**
     * The route that displays the JS form and will display the token
     * @Route(
     *     "/demo/security/login/jwt/frontend",
     *     name="demo_login_jwt",
     *     methods={"GET"})
     */
    public function form()
    {
        return $this->render('spa-quasar.html.twig', ['appName' => 'login', 'useParent' => true, ]);
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
     *     "/demo/security/login/jwt/isloggedin",
     *     name="demo_secured_page_jwt_is_logged_in",
     *     defaults={"_format"="json"},
     *     methods={"GET"}
     * )
     *
     * @param Request $request
     * @param JWTEncoderInterface $jwtEncoder
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function isLoggedIn(Request $request, JWTEncoderInterface $jwtEncoder)
    {
        // will be usefull if we decide to return always 200 + the real Json content represented by isLoggedIn: 0|1
        $authenticated = $this->isGranted('IS_AUTHENTICATED_FULLY');
        $data = ['isLoggedIn' => (int) $authenticated, ];

        if ($authenticated) {
            $user = $this->getUser();
            $data['me'] = [
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
                ];

            // check validity of the token
            $bearer = $request->headers->get('Authorization');
            $jwtEncoder->decode(substr($bearer, 7));
        }

        return new JsonResponse($data);
    }
}
