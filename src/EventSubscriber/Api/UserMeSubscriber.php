<?php
namespace App\EventSubscriber\Api;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * freely inspired by https://github.com/api-platform/core/issues/477 from lyrixx
 *
 * Class UserMeSubscriber
 * @package App\Api\EndPoint
 */
class UserMeSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['resolveMe', EventPriorities::PRE_READ],
        ];
    }

    public function resolveMe(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (true === !in_array($request->attributes->get('_route'), ['api_users_get_item', 'api_users_patch_item', 'api_users_put_item'])) {
            return;
        }

        if ('me' !== $request->attributes->get('id')) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token || !$token->getUser()) {
            return;
        }

        $request->attributes->set('id', $token->getUser()->getId());
    }
}
