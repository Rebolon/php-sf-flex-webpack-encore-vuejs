<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class SimpleController
{
    public function index(LoggerInterface $logger)
    {
        // with autowiring, no needs to inject the arguments, so no need to extend Controller except if you want
        // direct access to parameters from the container
        $logger->info('log from ' . __METHOD__);

        return new Response('This is the index action !');
    }
}
