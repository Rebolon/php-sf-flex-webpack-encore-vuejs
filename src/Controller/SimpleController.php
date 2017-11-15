<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class SimpleController
{
    public function index() {
        return new Response('This is the index action !');
    }

}