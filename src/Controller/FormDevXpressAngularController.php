<?php

namespace App\Controller;

use App\Tools\AngularCli;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class FormDevXpressAngularController extends Controller
{
    /**
     * @var string $kernelProjectDir
     * @Route("/demo/form/devxpress-angular/{ngRouteName}", requirements={"ngRouteName"=".*"}, defaults={"ngRouteName"="home"})
     * @Method({"GET"})
     * @Cache(maxage="2 weeks")
     * @return Response
     */
    public function index(string $kernelProjectDir)
    {
        $ngFiles = AngularCli::getNgBuildFiles($kernelProjectDir);

        return $this->render('form-devxpress-angular/app.html.twig', [
            'ngFiles' => $ngFiles,
            'appName' => 'devxpress-angular',
            'title' => 'DevxpressAngular',
            'preventParentJs' => true, // since Angular6, but maybe need to build nativeElement to solve this
            'useParent' => false,
            ]);
    }
}
