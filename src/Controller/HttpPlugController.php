<?php

namespace App\Controller;

use Http\Message\MessageFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Http\Client\HttpAsyncClient;

class HttpPlugController extends Controller
{
    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var HttpAsyncClient
     */
    protected $httpClient;

    /**
     * HttpPlugController constructor.
     *
     * @param HttpAsyncClient $client
     * @param MessageFactory $messageFactory
     */
    public function __construct(HttpAsyncClient $client, MessageFactory $messageFactory)
    {
        $this->httpClient = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @Cache(expires="+24 hour")
     * @Route("/demo/http-plug")
     * @Method({"GET"})
     */
    public function call()
    {
        $request = $this->messageFactory->createRequest('GET', 'https://ghibliapi.herokuapp.com/films');
        $promise = $this->httpClient->sendAsyncRequest($request);
        $response = $promise->wait();

        if ($response->getStatusCode() > 299) {
            throw new \HttpResponseException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return new JsonResponse($response->getBody(), 200, [], true);
    }
}
