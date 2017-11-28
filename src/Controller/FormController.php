<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FormController extends Controller
{
    /**
     * Try to access todos (will change the route when login is fine)
     *
     * @Route("/demo/form")
     * @Method({"GET"})
     */
    public function index() {
        return $this->render('form/app.html.twig', ['appName' => 'form', ]);
    }
}