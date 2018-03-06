<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Author;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class AuthorConverter extends AbstractConverter
{
    const NAME = 'author';

    /**
     * {@inheritdoc}
     */
    function getEzPropsName(): array
    {
        return ['id', 'firstname', 'lastname', ];
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
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $request->attributes->set($configuration->getName(), $this->initFromRequest($request->getContent()));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray)
    {
        try {
            $entity = new Author();
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
            throw new ValidationException($violationList,'Wrong parameter to create new Author (generic)', 420, $e);
        }
    }
}
