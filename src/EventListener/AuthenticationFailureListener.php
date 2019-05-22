<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationFailureListener
{
    /**
     * @var RequestStack
     */
    //private $requestStack;

    /**
     * I removed the arguments coz it makes it crashes : ArgumentCountError Too few arguments to function App\EventListener\AuthenticationFailureListener::__construct(), 0 passed in var\cache\dev\ContainerZllZMMP\srcApp_KernelDevDebugContainer.php on line 718 and exactly 1 expected
     * @param RequestStack $requestStack
     */
    public function __construct(/*RequestStack $requestStack*/)
    {
        //$this->requestStack = $requestStack;
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $data = [
            'status'  => '401 Unauthorized',
            'message' => 'Bad credentials, please verify that your username/password are correctly set',
        ];

        $response = new JWTAuthenticationFailureResponse($data);

        $event->setResponse($response);
    }
}
