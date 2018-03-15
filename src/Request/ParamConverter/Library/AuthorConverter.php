<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Author;
use Rebolon\Request\ParamConverter\AbstractConverter;
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
    public function getEzPropsName(): array
    {
        return ['id', 'firstname', 'lastname', ];
    }

    /**
     * {@inheritdoc}
     */
    public function getManyRelPropsName():array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOneRelPropsName():array
    {
        return [];
    }
}
