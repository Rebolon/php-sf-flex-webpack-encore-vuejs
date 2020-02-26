<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Library\Book;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * For debugging purpose and help to understand the Serialization problem, thanks @clement for this tips
 *
 * Class BookSubscriber
 * @package App\EventSubscriber
 */
final class BookSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['debugDenormalization', EventPriorities::POST_DESERIALIZE],
            KernelEvents::VIEW => ['debugDenormalizationAfterPersist', EventPriorities::POST_WRITE],
        ];
    }

    public function debugDenormalization(RequestEvent $event)
    {
        $book = $event->getRequest()->get('data');
        $method = $event->getRequest()->getMethod();

        if (!$book instanceof Book || Request::METHOD_POST !== $method) {
            return;
        }

        $this->logger->debug('DENORMALIZATION');
        $this->logger->debug('book: ' . $book->getTitle());
        $this->logger->debug('serie: ' . $book->getSerie()->getName());
        $this->logger->debug('#authors: ' . $book->getAuthors()->count());
        foreach ($book->getAuthors()->toArray() as $project) {
            $this->logger->debug("\t author: " . $project->getAuthor()->getFirstname());
        }
    }

    public function debugDenormalizationAfterPersist(ViewEvent $event)
    {
        $book = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$book instanceof Book || Request::METHOD_POST !== $method) {
            return;
        }

        $this->logger->debug('DENORMALIZATION_AFTER_PERSIST');
        $this->logger->debug('serie:' . $book->getSerie()->getName());
        $this->logger->debug('#authors: ' . $book->getAuthors()->count());
        foreach ($book->getAuthors()->toArray() as $author) {
            $this->logger->debug("\t author: " . $author->getAuthor()->getFirstname());
        }
    }
}
