<?php

namespace App\Request\ParamConverter\Library;

use App\Entity\Library\Job;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class JobConverter extends AbstractConverter
{
    const NAME = 'job';

    /**
     * {@inheritdoc}
     */
    function getEzPropsName(): array
    {
        return ['id', 'translation_key', ];
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
        $content = $request->getContent();

        if (!$content) {
            return false;
        }

        $raw = json_decode($content, true);
        if ($raw
            && array_key_exists(self::NAME, $raw)) {
            $request->attributes->set($configuration->getName(), $this->initFromRequest($raw[self::NAME]));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray)
    {
        try {
            $entity = new Job();
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
            throw new ValidationException($violationList,'Wrong parameter to create new Job (generic)', 420, $e);
        }
    }
}
