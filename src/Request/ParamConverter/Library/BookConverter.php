<?php

namespace App\Request\ParamConverter\Library;

use Psr\Log\LoggerInterface;
use Rebolon\Request\ParamConverter\ItemAbstractConverter;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Library\Book;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class BookConverter
 *
 * for instance we don't allow book node to be inside other nodes (projectBookedition/Creation or any aother entity)
 * if we decide to do that we will have to manage the case where $jsonOrArray is a string, so we will have to
 * retreive the entity
 *
 * @package App\Request\ParamConverter\Library
 */
class BookConverter extends ItemAbstractConverter
{
    const NAME = 'book';

    const RELATED_ENTITY = Book::class;

    /**
     * @var ProjectBookCreationConverter
     */
    protected $projectBookCreationConverter;

    /**
     * @var ProjectBookEditionConverter
     */
    protected $projectBookEditionConverter;

    /**
     * @var SerieConverter
     */
    protected $serieConverter;

    /**
     * BookConverter constructor.
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ProjectBookCreationConverter $projectBookCreationConverter
     * @param ProjectBookEditionConverter $projectBookEditionConverter
     * @param SerieConverter $serieConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ProjectBookCreationConverter $projectBookCreationConverter,
        ProjectBookEditionConverter $projectBookEditionConverter,
        SerieConverter $serieConverter,
        LoggerInterface $logger
    ) {
        parent::__construct($validator, $serializer, $entityManager);

        $this->projectBookCreationConverter = $projectBookCreationConverter;
        $this->projectBookEditionConverter = $projectBookEditionConverter;
        $this->serieConverter = $serieConverter;
        $this->constructorParams[] = $logger;
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "book": {
     *     "title": "The green lantern",
     *     "description": "Whatever you want",
     *     "index_in_serie": 15
     *   }
     * }
     */
    public function getEzPropsName(): array
    {
        return ['id', 'title', 'description', 'indexInSerie', ];
    }

    /**
     * {@inheritdoc}
     */
    public function getManyRelPropsName():array
    {
        // for instance i don't want to allow the creation of reviews with all embeded reviews, this is not a usual way of working
        // that's why i don't add reviews here
        return [
            'authors' => [
                'converter' => $this->projectBookCreationConverter, 'setter' => 'addAuthors',
                'cb' => function($relation, $entity) {
                    // not mandatory coz you may do this in in the entity->addAuthors but this is for sample of 'cb' operations
                    $relation->setBook($entity);
                },
            ],
            'editors' => [
                'converter' => $this->projectBookEditionConverter, 'setter' => 'addEditors',
                'cb' => function($relation, $entity) {
                    // not mandatory coz you may do this in in the entity->addAuthors but this is for sample of 'cb' operations
                    $relation->setBook($entity);
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * registryKey could be used if we create an endpoint that allow batch POST/PUT of book with embedded serie
     */
    public function getOneRelPropsName():array
    {
        return ['serie' => ['converter' => $this->serieConverter, 'registryKey' => 'serie', ], ];
    }
}
