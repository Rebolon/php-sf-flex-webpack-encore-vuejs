<?php

namespace App\Controller\Api;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Security\CsrfToken;

class TokenController extends AbstractController
{
    /**
     * For SPA, to get a new valid token
     *
     * @Route("/token", name="token")
     * @param Request $request
     * @param CsrfToken $csrfTokenManager
     * @param string $csrfTokenParameter
     * @param string $csrfTokenId
     * @return JsonResponse
     */
    public function token(
        Request $request,
        CsrfToken $csrfTokenManager,
        string $csrfTokenParameter,
        string $csrfTokenId
    ) {
        try {
            // get current token and return it if exists and valid
            $this->get(TokenController::class)->tokenCheck(
                $request,
                $csrfTokenManager,
                $csrfTokenParameter,
                $csrfTokenId
            );

            return new JsonResponse($request->get($csrfTokenParameter));
        } catch (Exception $e) {
            // else create a new one and return it
            $token = $csrfTokenManager->getToken($csrfTokenId);

            return new JsonResponse($token->getValue());
        }
    }

    /**
     * @Route("/token_check", name="token_check")
     * @param Request $request
     * @param CsrfToken $csrfTokenManager
     * @param string $csrfTokenParameter
     * @param string $csrfTokenId
     * @return Response
     */
    public function tokenCheck(
        Request $request,
        CsrfToken $csrfTokenManager,
        string $csrfTokenParameter,
        string $csrfTokenId
    ) {
        $tokenValue = $request->get($csrfTokenParameter);
        if (!$csrfTokenManager->tokenCheck($csrfTokenId, $tokenValue)) {
            throw new HttpException(400, 'Invalid token');
        }

        return new Response();
    }
}
