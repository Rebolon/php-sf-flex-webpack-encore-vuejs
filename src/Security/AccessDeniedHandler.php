<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// seen on this documentation https://symfony.com/doc/current/security/access_denied_handler.html
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

// but seems unrelated to json_login system :
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Class AccessDeniedHandler
 *
 * For Security Component, you need to implement your own AccessDeniedHandler
 *
 * @package App\Api\Security\Security
 */
// When i just implement AccessDeniedHandlerInterface as said on the doc, then i got a 500 :
// Type error: Argument 1 passed to Symfony\Component\Security\Http\Authentication\CustomAuthenticationFailureHandler::__construct() must implement interface Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface, instance of App\Security\AccessDeniedHandler given, called in C:\dev\projects\fiducial\sf-flex-encore-vuejs\var\cache\dev\ContainerI18QW6b\getSecurity_Authentication_Listener_Json_VuejsService.php on line 8
//class AccessDeniedHandler implements AccessDeniedHandlerInterface
class AccessDeniedHandler implements AuthenticationFailureHandlerInterface, AccessDeniedHandlerInterface
{
    protected function return403($message) {
        return new JsonResponse($message, 403);
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $message = $accessDeniedException->getMessage();

        return $this->return403('handle from AccessDeniedHandler' . $message);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = $exception->getMessage();

        return $this->return403('onAuthenticationFailure from AccessDeniedHandler' . $message);
    }
}
