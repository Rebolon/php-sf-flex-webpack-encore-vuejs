<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TokenController extends Controller
{
    /**
     * For SPA, to get a new valid token
     *
     * @Route("/token", name="token")
     * @param Request $request
     * @return JsonResponse
     */
    public function token(Request $request, CsrfTokenManagerInterface $tokenManager) {
        try {
            // get current token and return it if exists and valid
            $this->get(\App\Controller\Api\TokenController::class)->tokenCheck($request, $tokenManager);
            $tokenKey = $this->getParameter('csrf_token_parameter');

            return new JsonResponse($request->get($tokenKey));
        } catch (HttpException $e) {
            // else create a new one and return it
            $tokenId = $this->getParameter('csrf_token_id');
            $token = $tokenManager->getToken($tokenId);

            return new JsonResponse($token->getValue());
        }
    }

    /**
     * @Route("/token_check", name="token_check")
     * @param Request $request
     * @param CsrfTokenManagerInterface $tokenManager
     * @return Response
     */
    public function tokenCheck(Request $request, CsrfTokenManagerInterface $tokenManager) {
        $tokenId = $this->getParameter('csrf_token_id');
        $tokenKey = $this->getParameter('csrf_token_parameter');
        $tokenValue = $request->get($tokenKey);
        $token = new CsrfToken($tokenId, $tokenValue);

        if (!$tokenValue || !$tokenManager->isTokenValid($token)) {
            throw new HttpException(400, 'Invalid token');
        }

        return new Response();
    }
}