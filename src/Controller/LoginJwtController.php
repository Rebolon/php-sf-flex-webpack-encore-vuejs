<?php

namespace App\Controller;

use App\Security\JwtTokenTools;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class LoginJwtController extends Controller
{
    /**
     * Try to test this security when the one on the bottom works Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/demo/security/login/jwt/secured", name="demo_secured_page_jwt")
     * @Method({"GET"})
     */
    public function index()
    {
        $user = $this->getUser();

        return $this->render('login/index.html.twig', ['user' => $user, ]);
    }

    /**
     * The route that displays the JS form and will display the token
     * @Route("/demo/security/login/jwt/frontend")
     * @Method({"GET"})
     */
    public function form()
    {
        return $this->render('spa-quasar.html.twig', ['appName' => 'login', 'useParent' => true, ]);
    }

    /**
     * The route that generate token for a couple login/password
     * It works with Basic HTTP auth or with formData using login/password where path are store in parameters: login_username_path/login_password_path
     *
     * @Route("/demo/security/login/jwt/tokens",
     *     defaults={"_format"="json"})
     * @Method({"POST"})
     *
     * @param Request $request
     * @param InMemoryUserProvider $provider
     * @param JWTEncoderInterface $encoder
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoggerInterface $logger
     * @param JwtTokenTools $tokenTool
     * @return JsonResponse
     * @throws \Exception
     */
    public function newToken(
        Request $request,
        InMemoryUserProvider $provider,
        JWTEncoderInterface $encoder,
        UserPasswordEncoderInterface $passwordEncoder,
        LoggerInterface $logger,
        JwtTokenTools $tokenTool)
    {
        $username = $request->getUser() ?: $request->request->get($this->getParameter('login_username_path'));
        $password = $request->getPassword() ?: $request->request->get($this->getParameter('login_password_path'));

        if (!$username) {
            $json = json_decode($request->getContent(), true);
            if (!json_last_error()) {
                $username = $json[$this->getParameter('login_username_path')];
                $password = $json[$this->getParameter('login_password_path')];
            }
        }

        $token = $tokenTool->encodeToken(
            $provider,
            $encoder,
            $passwordEncoder,
            $this->getParameter('token_jwt_ttl'),
            $username,
            $password,
            $logger
        );

        return new JsonResponse(['token' => $this->getParameter('token_jwt_bearer') . ' ' . $token]);
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
     *     defaults={"_format"="json"}
     *     )
     * @Method({"GET"})
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
