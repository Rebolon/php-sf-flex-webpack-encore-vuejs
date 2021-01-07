<?php
namespace App\Action;

use App\Entity\Library\Book;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @deprecated this is not recommended by ApiPlatform, you should prefer the usage of DataProvider/DataPersister and extensions
 *
 * Class BookSpecial
 * @package App\Action
 */
class BookSpecial
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * BookSpecial constructor.
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface  $entityManager, RouterInterface $router, SerializerInterface $serializer)
    {
        $this->em = $entityManager;
        $this->router = $router;
        $this->serializer = $serializer;
    }

    /**
     * Custom route to do POST operation over Book entity with all nested relations
     * It uses ParamConverter usage to reduce the responsability of the controller
     *
     * @Route(
     *     name="book_special_sample3",
     *     path="/api/booksiu/special_3",
     *     methods={"POST"}
     * )
     * @ParamConverter(name="book", converter="book")
     *
     * @param Book $book
     * @return JsonResponse|Response
     */
    public function special3(Book $book)
    {
        if ($book) {
            $this->em->persist($book);

            $this->em->flush();

            $response = $this->serializer->serialize($book, 'json');
        } else {
            return new Response('No Content', 204);
        }

        // todo return a 201 with iris to book, use the router to build the iris
        return new JsonResponse($response, 201, [], true);
    }
}
