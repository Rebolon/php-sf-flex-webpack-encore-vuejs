<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Job;
use App\Request\ParamConverter\AbstractConverter;

class JobConverter extends AbstractConverter
{
    const NAME = 'job';

    const RELATED_ENTITY = Job::class;

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "role": {
     *     "translation_key": 'WRITER'
     *   }
     * }
     */
    function getEzPropsName(): array
    {
        return ['id', 'translation_key', ];
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
}
