<?php

namespace App\Controller;

use Http\Client\Exception\HttpException;
use Http\Message\MessageFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Http\Client\HttpAsyncClient;

class HttpPlugController extends AbstractController
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
     * @Route(
     *     "/demo/http-plug",
     *     methods={"GET"}
     *     )
     */
    public function call()
    {
        $request = $this->messageFactory->createRequest('GET', 'https://ghibliapi.herokuapp.com/films');
        $promise = $this->httpClient->sendAsyncRequest($request);
        $response = $promise->wait();

        if ($response->getStatusCode() > 299) {
            throw new HttpException("Api ghibli returned bad response", $request, $response);
        }

        return new JsonResponse($response->getBody(), 200, [], true);
    }
}
