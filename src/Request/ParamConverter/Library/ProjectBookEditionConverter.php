<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\ProjectBookEdition;
use Psr\Log\LoggerInterface;
use Rebolon\Request\ParamConverter\ListAbstractConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectBookEditionConverter extends ListAbstractConverter
{
    const NAME = 'editors';

    const RELATED_ENTITY = ProjectBookEdition::class;

    /**
     * @var EditorConverter
     */
    protected $editorConverter;

    /**
     * ProjectBookEditionConverter constructor.
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param EditorConverter $editorConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        EditorConverter $editorConverter,
        LoggerInterface $logger
    ) {
        parent::__construct($validator, $serializer, $entityManager);

        $this->editorConverter = $editorConverter;
        $this->constructorParams[] = $logger;
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "editors": {
     *     "publicationsDate": "1519664915",
     *     "collection": "A collection or edition name of the publication",
     *     "isbn": '2-87764-257-7',
     *     "editor": {
     *       ...
     *     }
     *   }
     * }
     */
    public function getEzPropsName(): array
    {
        return ['id', 'publicationDate', 'collection', 'isbn', ];
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
     * for this kind of json:
     * {
     *   "editors": {
     *     "editor": { ... }
     *   }
     * }
     */
    public function getOneRelPropsName():array
    {
        // for instance i don't want to allow the creation of a serie with all embeded books, this is not a usual way of working
        // that's why i don't add books here
        return ['editor' => ['converter' => $this->editorConverter, 'registryKey' => 'editor', ], ];
    }
}
