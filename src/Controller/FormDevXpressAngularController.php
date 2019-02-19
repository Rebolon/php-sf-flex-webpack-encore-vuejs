<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FormDevXpressAngularController extends AbstractController
{
    /**
     * @Route(
     *     "/demo/form/devxpress-angular/{ngRouteName}",
     *     requirements={"ngRouteName"=".*"},
     *     defaults={"ngRouteName"="home"},
     *     methods={"GET"}
     *     )
     */
    public function index()
    {
        return $this->render('form-devxpress-angular/app.html.twig', [
            'appName' => 'devxpress-angular',
            'title' => 'DevxpressAngular',
            'preventParentJs' => true, // since Angular6, but maybe need to build nativeElement to solve this
            'useParent' => true,
            ]);
    }
}
