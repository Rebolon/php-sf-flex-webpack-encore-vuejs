<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\ProjectBookCreation;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectBookCreationConverter extends AbstractConverter
{
    const NAME = 'authors';

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
     * @param JobConverter $jobConverter
     * @param AuthorConverter $authorConverter
     */
    public function __construct(
        ValidatorInterface $validator, SerializerInterface $serializer,
        JobConverter $jobConverter, AuthorConverter $authorConverter
    ) {
        parent::__construct($validator, $serializer);

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
     * @todo check if $json is an array or a string with iris like /api/author/15 => retreive the author and set it
     *
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray)
    {
        $json = $this->checkJsonOrArray($jsonOrArray);

        // if json is a string then retreive the entity with Doctrine and do nothing else except return the entity

        // else do the build calls:

        // the API accept authors as one object or as an array of object, so i need to transform at least in one array
        $authors = $json;
        if (is_object($json)) {
            $authors = [$json];
        }

        $entities = [];
        foreach ($authors as $author) {
            try {
                $entity = new ProjectBookCreation();

                $this->buildWithEzProps($author, $entity);

                $this->buildWithManyRelProps($author, $entity);

                //@todo how to manage the fact that the author is the same between 2 projects ?
                // i have to find a way to create only one author in that case !
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
