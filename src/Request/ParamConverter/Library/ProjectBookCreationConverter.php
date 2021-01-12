<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\ProjectBookCreation;
use Doctrine\ORM\EntityManagerInterface;
use Rebolon\Request\ParamConverter\ListAbstractConverter;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectBookCreationConverter extends ListAbstractConverter
{
    const NAME = 'authors';

    const RELATED_ENTITY = ProjectBookCreation::class;

    /**
     * @var JobConverter
     */
    protected $jobConverter;

    /**
     * @var AuthorConverter
     */
    protected $authorConverter;

    /**
     * ProjectBookCreationConverter constructor.
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param JobConverter $jobConverter
     * @param AuthorConverter $authorConverter
     */
    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        JobConverter $jobConverter,
        AuthorConverter $authorConverter
    ) {
        parent::__construct($validator, $serializer, $entityManager);

        $this->jobConverter = $jobConverter;
        $this->authorConverter = $authorConverter;
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "authors": {
     *     "job": { ... },
     *     "author": { ... },
     *   }
     * }
     */
    public function getEzPropsName(): array
    {
        return ['id', ];
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
        // for instance i don't want to allow the creation of a serie with all embeded books, this is not a usual way of working
        // that's why i don't add books here
        return [
            'role' => ['converter' => $this->jobConverter, 'registryKey' => 'role', ],
            'author' => ['converter' => $this->authorConverter, 'registryKey' => 'author', ],
            ];
    }
}
