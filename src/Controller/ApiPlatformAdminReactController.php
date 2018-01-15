<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ApiPlatformAdminReactController extends Controller
{
    /**
     * @Route("/demo/api-platform-admin-react")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('api-platform-admin-react/app.html.twig', ['appName' => 'api-platform-admin-react', ]);
    }
}
