<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Job;
use Rebolon\Request\ParamConverter\AbstractConverter;

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
    public function getEzPropsName(): array
    {
        return ['id', 'translation_key', ];
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
