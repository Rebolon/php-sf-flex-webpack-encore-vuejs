<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ApiPlatformReactAdminController extends Controller
{
    /**
     * @Route("/demo/api-platform-admin")
     * @Method({"GET"})
     */
    public function index() {
        return $this->render('api-platform-admin/app.html.twig', ['appName' => 'api-platform-admin-react', 'useParent' => true, ]);
    }

}