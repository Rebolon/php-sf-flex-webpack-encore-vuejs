<?php
namespace App\Action;

use App\Entity\Library\Book;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @todo Doesn't seem to be catched by Api-Platform
 *
 * Class BookSpecial
 * @package App\Action
 */
class BookSpecial
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SerializerInterface
     */
    private $serializer;

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
     * @Route(
     *     name="book_special_sample1",
     *     path="/api/books/{id}/special_1",
     *     defaults={"_api_resource_class"=Book::class, "_api_item_operation_name"="special_1"}
     * )
     * @Method("GET")
     *
     * @param Book $data
     * @return Book
     *
     * API Platform retrieves the PHP entity using the data provider then (for POST and
     * PUT method) deserializes user data in it. Then passes it to the action. Here $data
     * is an instance of Book having the given ID. By convention, the action's parameter
     * must be called $data.
     */
    public function special1(Book $data)
    {
        // API Platform will automatically validate, persist (if you use Doctrine) and serialize an entity
        // for you. If you prefer to do it yourself, return an instance of Symfony\Component\HttpFoundation\Response
        return $data;
    }

    /**
     * @Route(
     *     name="book_special_sample2",
     *     path="/api/books/{id}/special_2",
     *     defaults={"_api_resource_class"=Book::class, "_api_item_operation_name"="special_2"}
     * )
     * @Method("GET")
     *
     * @param Book $data
     * @return JsonResponse
     */
    public function special2(Book $data)
    {
        $newData = [
            "id" => $data->getId(),
            "title" => $data->getTitle(),
            "description" => $data->getDescription(),
            "indexInSerie" => $data->getIndexInSerie(),
            "extra" => "extra infos",
        ];

        $serie = $data->getSerie();
        $newData['serie'] = [
            "id" => $serie->getId(),
            "name" => $serie->getName(),
        ];

        $editors = $data->getEditors();
        foreach ($editors as $editor) {
            $newData['editors'][] = [
                "collection" => $editor->getCollection(),
                "id" => $editor->getEditor()->getId(),
                "name" => $editor->getEditor()->getName(),
            ];
        }

        /**
        "reviews" => $data->getReviews(), // manage pagination ?
        "serie" => $data->getSerie(),
        "authors" => $data->getAuthors(),
        "editors" => $data->getEditors(),
         */

        return new JsonResponse($newData);
    }

    /**
     * Custom route to do POST operation over Book entity with all nested relations
     * It uses ParamConverter usage to reduce the responsability of the controller
     *
     * @Route(
     *     name="book_special_sample3",
     *     path="/api/booksiu/special_3"
     * )
     * @ParamConverter(name="book", converter="book")
     * @Method("POST")
     *
     * @param Book $book
     * @return JsonResponse
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
