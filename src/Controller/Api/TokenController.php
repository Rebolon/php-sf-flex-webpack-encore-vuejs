<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Security\CsrfToken;

class TokenController extends Controller
{
    /**
     * For SPA, to get a new valid token
     *
     * @Route("/token", name="token")
     * @param Request $request
     * @param \App\Security\CsrfToken $csrfTokenManager
     * @return JsonResponse
     */
    public function token(Request $request, CsrfToken $csrfTokenManager)
    {
        try {
            // get current token and return it if exists and valid
            $this->get(TokenController::class)->tokenCheck($request, $csrfTokenManager);
            $tokenKey = $this->getParameter('csrf_token_parameter');

            return new JsonResponse($request->get($tokenKey));
        } catch (\Exception $e) {
            // else create a new one and return it
            $tokenId = $this->getParameter('csrf_token_id');
            $token = $csrfTokenManager->getToken($tokenId);

            return new JsonResponse($token->getValue());
        }
    }

    /**
     * @Route("/token_check", name="token_check")
     * @param Request $request
     * @param \App\Security\CsrfToken $csrfTokenManager
     * @return Response
     */
    public function tokenCheck(Request $request, CsrfToken $csrfTokenManager)
    {
        $tokenId = $this->getParameter('csrf_token_id');
        $tokenKey = $this->getParameter('csrf_token_parameter');
        $tokenValue = $request->get($tokenKey);

        if (!$csrfTokenManager->tokenCheck($tokenId, $tokenValue)) {
            throw new HttpException(400, 'Invalid token');
        }

        return new Response();
    }
}
