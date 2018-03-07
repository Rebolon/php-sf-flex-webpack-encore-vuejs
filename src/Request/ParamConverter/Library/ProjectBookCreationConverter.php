<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\ProjectBookCreation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectBookCreationConverter extends AbstractConverter
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
        ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager,
        JobConverter $jobConverter, AuthorConverter $authorConverter
    ) {
        parent::__construct($validator, $serializer, $entityManager);

        $this->jobConverter = $jobConverter;
        $this->authorConverter = $authorConverter;
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "authors": { }
     * }
     */
    function getEzPropsName(): array
    {
        return ['id', ];
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
        // for instance i don't want to allow the creation of a serie with all embeded books, this is not a usual way of working
        // that's why i don't add books here
        return [
            'role' => ['converter' => $this->jobConverter, 'registryKey' => 'role', ],
            'author' => ['converter' => $this->authorConverter, 'registryKey' => 'author', ],
            ];
    }

    /**
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray)
    {
        $json = $this->checkJsonOrArray($jsonOrArray);

        // the API accept authors as one object or as an array of object, so i need to transform at least in one array
        $authors = $json;
        if (is_object($json)) {
            $authors = [$json];
        }

        $entities = [];
        foreach ($authors as $author) {
            try {
                // ProjectBookCreation is an associative db table, so it can not be reused, that's why we d'ont reuse parent::initFromRequest
                $className = static::RELATED_ENTITY;
                $entity = new $className;

                $this->buildWithEzProps($author, $entity);

                $this->buildWithManyRelProps($author, $entity);

                $this->buildWithOneRelProps($author, $entity);

                $errors = $this->validator->validate($entity);

                if (count($errors)) {
                    throw new ValidationException($errors);
                }

                $entities[] = $entity;
            } catch (ValidationException $e) {
                throw $e;
            } catch (\Exception $e) {
                $violationList = new ConstraintViolationList();
                $violation = new ConstraintViolation($e->getMessage(), null, [], null, null, null);
                $violationList->add($violation);
                throw new ValidationException($violationList,'Wrong parameter to create new Authors (generic)', 420, $e);
            }
        }

        return $entities;
    }
}
