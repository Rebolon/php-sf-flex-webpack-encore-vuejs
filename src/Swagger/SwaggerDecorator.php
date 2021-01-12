<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $decorated;

    /**
     * @var string
     */
    protected $loginUsernamePath;

    /**
     * @var string
     */
    protected $loginPasswordPath;

    /**
     * SwaggerDecorator constructor.
     * @param NormalizerInterface $decorated
     * @param string $loginUsernamePath
     * @param string $loginPasswordPath
     */
    public function __construct(NormalizerInterface $decorated, $loginUsernamePath, $loginPasswordPath)
    {
        $this->decorated = $decorated;
        $this->loginUsernamePath = $loginUsernamePath;
        $this->loginPasswordPath = $loginPasswordPath;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

/*        $authDefinition = [
            [
                'name' => $this->loginUsernamePath,
                'description' => 'Username for the current account',
                'required' => true,
                'type' => 'string',
            ],
            [
                'name' => $this->loginPasswordPath,
                'description' => 'Password for the current account',
                'required' => true,
                'type' => 'string',
            ]
        ];

        // e.g. add a custom parameter
        $docs['paths']['/demo/security/login/jwt/authenticate']['post'] = [
            'parameters' => $authDefinition,
            'summary' => 'Performs a login attempt, returning a valid token on success and extra informations in payload',
            'responses' => [
                '200' => [
                    'description' => "Authentication response",
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'array',
                                'items' => [
                                    '$ref' => '#/components/schemas/Authentication-authentication:read',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'consumes' => ['application/json'],
            'produces' => ['application/json'],
            'tags' => ['Login'],
        ];

        $docs['components']['schemas']['Authentication-authentication:read'] = [
            'type' => 'object',
            'description' => 'Class Auth',
            'properties' => [
                'token' => [
                    "type" => "string",
                    'description' => 'This encrypted JWT token may contain all those informations: iat (int, creation date), exp (int, expiration date), roles (array), username (string), ip (IP)',
                ],
                'data' => [
                    "type" => "object",
                    '$ref' => '#/components/schemas/Authentication-data:read'
                ],
            ]
        ];

        $docs['components']['schemas']['Authentication-data:read'] = [
            'type' => 'object',
            'description' => 'Class AuthData',
            'properties' => [
                'roles' => [
                    "type" => "array",
                ]
            ]
        ];
*/
        $bookDefinition = [
            'name' => 'books',
            'definition' => <<<DEFINITION
{
    "book": {
        "title": "string",
        "editors": [{
            "publication_date": "date",
            "collection": "string",
            "isbn": "string",
            "editor": editor
        },],
        "authors": [{
            "role": job,
            "author": author
        },],
        "serie": serie
    }
}
DEFINITION
            ,
            'default' => 'id',
            'in' => 'body',
            'summary' => 'Create a Book resources with all related information so it can also create Editor or Author if their descriptions is embeded in the Book param',

        ];


        // e.g. add a custom parameter
        $docs['paths']['/api/booksiu/special_3']['post']['parameters'][] = $bookDefinition;

        $docs['paths']['/api/booksiu/special_3']['post']['consumes'] = ['application/json'];
        $docs['paths']['/api/booksiu/special_3']['post']['produces'] = ['application/json'];
        $docs['paths']['/api/booksiu/special_3']['post']['tags'] = ['Book', 'Editor', 'Author', 'Serie', ];

        // Override title
        $docs['info']['title'] = 'My Comic Library (title modified by SwaggerDecorator)';

        return $docs;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
