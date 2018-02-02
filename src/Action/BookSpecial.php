<?php
namespace App\Action;

use App\Entity\Library\Book;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo Doesn't seem to be catched by Api-Platform
 *
 * Class BookSpecial
 * @package App\Action
 */
class BookSpecial
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManagerInterface  $entityManager)
    {
        // for instance i inject the EntityManagerInterface to be able to do specific query and return them throught a JsonResponse
        $this->em = $entityManager;
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
     */
    public function get(Book $data) // API Platform retrieves the PHP entity using the data provider then (for POST and
                                    // PUT method) deserializes user data in it. Then passes it to the action. Here $data
                                    // is an instance of Book having the given ID. By convention, the action's parameter
                                    // must be called $data.
    {
        return $data; // API Platform will automatically validate, persist (if you use Doctrine) and serialize an entity
                      // for you. If you prefer to do it yourself, return an instance of Symfony\Component\HttpFoundation\Response
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
     * @return Book
     */
    public function post(Book $data) // API Platform retrieves the PHP entity using the data provider then (for POST and
        // PUT method) deserializes user data in it. Then passes it to the action. Here $data
        // is an instance of Book having the given ID. By convention, the action's parameter
        // must be called $data.
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
        "reviews" => $data->getReviews(),
        "serie" => $data->getSerie(),
        "authors" => $data->getAuthors(),
        "editors" => $data->getEditors(),
         */


        return new JsonResponse($newData);
    }
}
