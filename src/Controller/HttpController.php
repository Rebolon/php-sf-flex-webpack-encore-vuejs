<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HttpController extends AbstractController
{
    /**
     * HttpController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @Cache(expires="+24 hour")
     * @Route("/demo/http", methods={"GET"})
     */
    public function call()
    {
        $uri = 'https://ghibliapi.herokuapp.com/films';
        $doCall = function ($client, $uri) {
            $request = $client->request('GET', $uri);

            if ($request->getStatusCode() > 299) {
                throw new ClientException($request);
            }

            return $request;
        };
        $client = HttpClient::create();

        try {
            $request = $doCall($client, $uri);
        } catch (ClientException $e) {
            throw $e;
        } catch (\Exception $e) {
            $client = new NativeHttpClient();
            $request = $doCall($client, $uri);
        }

        return new JsonResponse($request->getContent(), 200, [], true);
    }
}
