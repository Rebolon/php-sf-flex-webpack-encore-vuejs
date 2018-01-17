<?php
namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

/**
 * Class ApiAuthSubscriber
 *
 * This is for the Api-Platform Exception management. An issue has been created : https://github.com/api-platform/api-platform/issues/519
 * I'll try to modify the behavior of ApiPlatform like Dunglas asked but for instance this is a Subscriber that can manage
 * this for me.
 *
 * @todo remove this class if the solution of Dunglas is OK and merged into master
 *
 * @package App\EventSubscriber
 */
final class ApiAuthSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['from500to405', EventPriorities::PRE_RESPOND],
        ];
    }

    public function from500to405(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof AccessDeniedException
        || $exception instanceof InsufficientAuthenticationException) {
            $httpException = new HttpException(403, $exception->getMessage(), $exception->getPrevious());
            $event->setException($httpException);
        }
    }
}
