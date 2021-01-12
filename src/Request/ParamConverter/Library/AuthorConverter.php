<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Author;
use Rebolon\Request\ParamConverter\ItemAbstractConverter;

class AuthorConverter extends ItemAbstractConverter
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
