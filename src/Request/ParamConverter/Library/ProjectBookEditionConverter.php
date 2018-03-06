<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\ProjectBookEdition;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectBookEditionConverter extends AbstractConverter
{
    const NAME = 'editors';

    /**
     * @var EditorConverter
     */
    protected $editorConverter;

    /**
     * ProjectBookEditionConverter constructor.
     * @param ValidatorInterface $validator
     * @param EditorConverter $editorConverter
     */
    public function __construct(
        ValidatorInterface $validator, SerializerInterface $serializer,
        EditorConverter $editorConverter
    ) {
        parent::__construct($validator, $serializer);

        $this->editorConverter = $editorConverter;
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "editors": {
     *     "publications_date": "1519664915",
     *     "collection": "A collection or edition name of the publication",
     *     "isbn": '2-87764-257-7'
     *   }
     * }
     */
    function getEzPropsName(): array
    {
        return ['id', 'publication_date', 'collection', 'isbn', ];
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
     * for this kind of json:
     * {
     *   "editors": {
     *     "editor": { ... }
     *   }
     * }
     */
    function getOneRelPropsName():array {
        // for instance i don't want to allow the creation of a serie with all embeded books, this is not a usual way of working
        // that's why i don't add books here
        return ['editor' => ['converter' => $this->editorConverter, 'registryKey' => 'editor', ], ];
    }

    /**
     * @todo check if $json is an array or a string with iris like /api/editor/15 => retreive the editor and set it
     *
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray)
    {
        $json = $this->checkJsonOrArray($jsonOrArray);

        // if json is a string then retreive the entity with Doctrine and do nothing else except return the entity

        // else do the build calls:

        // the API accept editors as one object or as an array of object, so i need to transform at least in one array
        $editors = $json;
        if (is_object($json)) {
            $editors = [$json];
        }

        $entities = [];
        foreach ($editors as $editor) {
            try {
                $entity = new ProjectBookEdition();

                $this->buildWithEzProps($editor, $entity);

                $this->buildWithManyRelProps($editor, $entity);

                $this->buildWithOneRelProps($editor, $entity);

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
                throw new ValidationException($violationList,'Wrong parameter to create new Editors (generic)', 420, $e);
            }
        }

        return $entities;
    }
}
