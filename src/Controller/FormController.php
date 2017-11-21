<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/demo/form")
     * @Method({"GET"})
     */
    public function index() {
        return $this->render('form/app.html.twig', ['appName' => 'form', ]);
    }

    /**
     * @Route("login")
     * @Method({"GET"})
     */
    public function login() {
        return $this->index();
    }

}