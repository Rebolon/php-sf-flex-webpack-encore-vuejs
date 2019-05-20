<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VuejsController extends AbstractController
{
    /**
     * @Route(
     *     "/demo/vuejs",
     *     methods={"GET"})
     * @Cache(maxage="2 weeks")
     */
    public function index()
    {
        return $this->render('vuejs/app.html.twig', ['appName' => 'vuejs', 'useParent' => true, ]);
    }
}
