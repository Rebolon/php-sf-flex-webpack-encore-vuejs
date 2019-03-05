<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Author;
use App\Entity\Library\Tag;
use Rebolon\Request\ParamConverter\ItemAbstractConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class TagConverter extends ItemAbstractConverter
{
    const NAME = 'tag';

    const RELATED_ENTITY = Tag::class;

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "tag": {
     *     "name": "Manga"
     *   }
     * }
     */
    public function getEzPropsName(): array
    {
        return ['id', 'name', ];
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
