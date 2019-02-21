<?php

namespace App\Controller;

use App\Tools\AngularCli;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Routing\Annotation\Route;

class FormDevXpressAngularController extends AbstractController
{
    /**
     * @var string $kernelProjectDir
     * @Route(
     *     "/demo/form/devxpress-angular/{ngRouteName}",
     *     requirements={"ngRouteName"=".*"},
     *     defaults={"ngRouteName"="home"},
     *     methods={"GET"}
     *     )
     * @Cache(maxage="2 weeks")
     */
    public function index(string $kernelProjectDir)
    {
        $ngFiles = AngularCli::getNgBuildFiles($kernelProjectDir);

        return $this->render('form-devxpress-angular/app.html.twig', [
            'ngFiles' => $ngFiles,
            'appName' => 'devxpress-angular',
            'title' => 'DevxpressAngular',
            'preventParentJs' => true, // since Angular6, but maybe need to build nativeElement to solve this
            'useParent' => true,
            ]);
    }
}
