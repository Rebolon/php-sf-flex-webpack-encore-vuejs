<?php

namespace App\Request\ParamConverter\Library;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\Library\Editor;

class EditorConverter extends AbstractConverter
{
    const NAME = 'editor';

    /**
     * {@inheritdoc}
     */
    function getEzPropsName(): array
    {
        return ['id', 'name', ];
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

    /**
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray)
    {
        try {
            $entity = new Editor();
            $json = $this->checkJsonOrArray($jsonOrArray);

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
            throw new ValidationException($violationList,'Wrong parameter to create new Editor (generic)', 420, $e);
        }
    }
}
