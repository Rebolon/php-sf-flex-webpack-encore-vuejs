<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{
    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $customDefinition = [
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
        $docs['paths']['/api/booksiu/special_3']['post']['parameters'][] = $customDefinition;

        $docs['paths']['/api/booksiu/special_3']['post']['consumes'] = ['application/json'];
        $docs['paths']['/api/booksiu/special_3']['post']['produces'] = ['application/json'];
        $docs['paths']['/api/booksiu/special_3']['post']['tags'] = ['Book', 'Editor', 'Author', 'Serie', ];

        // Override title
        $docs['info']['title'] = 'My Api Foo';

        return $docs;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
