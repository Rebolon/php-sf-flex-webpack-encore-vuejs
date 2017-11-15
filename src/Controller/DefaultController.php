<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index() {
        // @TODO list all routes from Router
        return new Response('This is the index action !');
    }

}