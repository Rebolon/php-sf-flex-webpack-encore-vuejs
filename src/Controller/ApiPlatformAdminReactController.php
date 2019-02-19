<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiPlatformAdminReactController extends AbstractController
{
    /**
     * @Route(
     *     "/demo/api-platform-admin-react",
     *     methods={"GET"}
     *     )
     */
    public function index()
    {
        return $this->render('api-platform-admin-react/app.html.twig', ['appName' => 'api-platform-admin-react', ]);
    }
}
