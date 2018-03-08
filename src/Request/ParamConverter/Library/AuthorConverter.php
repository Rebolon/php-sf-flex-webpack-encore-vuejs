<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Author;
use App\Request\ParamConverter\AbstractConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class AuthorConverter extends AbstractConverter
{
    const NAME = 'author';

    const RELATED_ENTITY = Author::class;

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "author": {
     *     "firstname": "Paul",
     *     "lastname": "Smith"
     *   }
     * }
     */
    function getEzPropsName(): array
    {
        return ['id', 'firstname', 'lastname', ];
    }

    /**
     * {@inheritdoc}
     */
    function getManyRelPropsName():array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    function getOneRelPropsName():array {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $request->attributes->set($configuration->getName(), $this->initFromRequest($request->getContent()));

        return true;
    }
}
