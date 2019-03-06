<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiPlatformAdminReactController extends AbstractController
{
    /**
     * @Route(
     *     "/demo/api-platform-admin-react",
     *     methods={"GET"}
     *     )
     * @Cache(maxage="2 weeks")
     */
    public function index()
    {
        return $this->render('api-platform-admin-react/app.html.twig', ['appName' => 'api-platform-admin-react', ]);
    }
}
