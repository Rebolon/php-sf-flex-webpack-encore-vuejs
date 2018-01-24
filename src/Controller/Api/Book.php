<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class Book extends Controller
{
    /**
     * @Route("/api/books/{id}/special", name="book_special", requirements={"id": "\d*"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function specialAction($id)
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse(['ca marche' => 'oui', ]);
    }
}
