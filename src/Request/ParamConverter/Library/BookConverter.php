<?php

namespace App\Request\ParamConverter\Library;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\Library\Book;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BookConverter extends AbstractConverter
{
    const NAME = 'book';

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
     * @param ProjectBookCreationConverter $projectBookCreationConverter
     * @param ProjectBookEditionConverter $projectBookEditionConverter
     * @param SerieConverter $serieConverter
     */
    public function __construct(
        ValidatorInterface $validator, SerializerInterface $serializer,
        ProjectBookCreationConverter $projectBookCreationConverter,
        ProjectBookEditionConverter $projectBookEditionConverter, SerieConverter $serieConverter
    ) {
        parent::__construct($validator, $serializer);

        $this->projectBookCreationConverter = $projectBookCreationConverter;
        $this->projectBookEditionConverter = $projectBookEditionConverter;
        $this->serieConverter = $serieConverter;
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
    function getEzPropsName(): array
    {
        return ['id', 'title', 'description', 'index_in_serie', ];
    }

    /**
     * {@inheritdoc}
     */
    function getManyRelPropsName():array
    {
        // for instance i don't want to allow the creation of reviews with all embeded reviews, this is not a usual way of working
        // that's why i don't add reviews here
        return [
            'authors' => [
                'converter' => $this->projectBookCreationConverter, 'setter' => 'setAuthor',
                'cb' => function ($relation, $entity) {
                    $relation->setBook($entity);
                },
            ],
            'editors' => [
                'converter' => $this->projectBookEditionConverter, 'setter' => 'setEditor',
                'cb' => function ($relation, $entity) {
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
    function getOneRelPropsName():array {
        return ['serie' => ['converter' => $this->serieConverter, 'registryKey' => 'serie', ], ];
    }

    /**
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray)
    {
        try {
            $entity = new Book();
            $json = $this->checkJsonOrArray($jsonOrArray);

            // for instance we don't allow book node to be inside other nodes (projectBookedition/Creation or any aother entity)
            // if we decide to do that we will have to manage the case where $jsonOrArray is a string, so we will have to
            // retreive the entity
            $this->buildWithEzProps($json, $entity);

            $this->buildWithManyRelProps($json, $entity);

            $this->buildWithOneRelProps($json, $entity);

            $errors = $this->validator->validate($entity);

            if (count($errors)) {
                throw new ValidationException($errors);
            }

            return $entity;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $violationList = new ConstraintViolationList();
            $violation = new ConstraintViolation($e->getMessage(), null, [], null, null, null);
            $violationList->add($violation);
            throw new ValidationException($violationList,'Wrong parameter to create new Book (generic)', 420, $e);
        }
    }
}
