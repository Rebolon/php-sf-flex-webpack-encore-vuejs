<?php

namespace App\Request\ParamConverter;

use App\Entity\Library\LibraryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;

/**
 * @todo there is maybe a way to mutualize the 3 methods buildWith*
 *
 * Class AbstractConverter
 * @package App\Request\ParamConverter\Library
 */
abstract class AbstractConverter implements ConverterInterface
{
    /**
     * @var array
     */
    static protected $registry = [];

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected $accessor;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    static protected $propertyPath = [];

    /**
     * AbstractConverter constructor.
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() === static::NAME;
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

        if (json_last_error()) {
            $violationList = new ConstraintViolationList();
            $violation = new ConstraintViolation(sprintf('JSON Error %s', json_last_error_msg()), null, [], null, $this->getPropertyPath(), null);
            $violationList->add($violation);
            throw new ValidationException(
                $violationList,
                sprintf('Wrong parameter to create new %s (generic)', static::RELATED_ENTITY),
                420
            );

            return false;
        }

        if ($raw
            && array_key_exists(static::NAME, $raw)) {
            $request->attributes->set($configuration->getName(), $this->initFromRequest($raw[static::NAME], static::NAME));
        }

        return true;
    }


    /**
     * @inheritdoc
     */
    public function initFromRequest($jsonOrArray, $propertyPath)
    {
        try {
            self::$propertyPath[] = $propertyPath;

            $json = $this->checkJsonOrArray($jsonOrArray);

            if (!is_array($json)) {
                array_pop(self::$propertyPath);

                return $this->getFromDatabase($json);
            }

            $entity = $this->buildEntity($json);

            array_pop(self::$propertyPath);

            return $entity;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $violationList = new ConstraintViolationList();
            $violation = new ConstraintViolation($e->getMessage(), null, [], null, $this->getPropertyPath(), null);
            $violationList->add($violation);
            throw new ValidationException(
                $violationList,
                sprintf('Wrong parameter to create new %s (generic)', static::RELATED_ENTITY),
                420,
                $e
            );
        }
    }


    /**
     * @param $json
     * @return mixed
     * @throws RuntimeException
     * @throws \TypeError
     */
    protected function buildEntity($json)
    {
        $className = static::RELATED_ENTITY;
        $entity = new $className();

        $this->buildWithEzProps($json, $entity);
        $this->buildWithManyRelProps($json, $entity);
        $this->buildWithOneRelProps($json, $entity);

        $errors = $this->validator->validate($entity);
        if (count($errors)) {
            throw new ValidationException($errors);
        }

        return $entity;
    }

    /**
     * Used for simple property that is not linked to other entities with relation like ManyTo OneTo...
     *
     * @param array $json
     * @param LibraryInterface $entity
     * @return LibraryInterface
     *
     * @throws \TypeError
     */
    private function buildWithEzProps(array $json, LibraryInterface $entity): LibraryInterface
    {
        $ezProps = $this->getEzPropsName();
        foreach ($ezProps as $prop) {
            if (!array_key_exists($prop, $json)
                || $json[$prop] === null) {
                continue;
            }

            $this->accessor->setValue($entity, $prop, $json[$prop]);
        }

        return $entity;
    }

    /**
     * @param array $json
     * @param LibraryInterface $entity
     * @return LibraryInterface
     * @throws RuntimeException
     */
    private function buildWithManyRelProps(array $json, LibraryInterface $entity): LibraryInterface
    {
        $relManyProps = $this->getManyRelPropsName();
        foreach ($relManyProps as $prop => $operationsInfo) {
            if (!array_key_exists($prop, $json)
                || $json[$prop] === null) {
                continue;
            }

            $this->checkOperationsInfo($operationsInfo, 'getManyRelPropsName');

            $relations = $operationsInfo['converter']->initFromRequest($json[$prop], $prop);

            // I don't fond a quick way to use the propertyAccessor so i keep this for instance
            $methodName = array_key_exists('setter', $operationsInfo) ? $operationsInfo['setter'] : null;
            foreach ($relations as $relation) {
                if (array_key_exists('cb', $operationsInfo)) {
                    if (!is_callable($operationsInfo['cb'])) {
                        throw new RuntimeException('cb in operations info must be callable');
                    }

                    $operationsInfo['cb']($relation, $entity);
                }

                if ($methodName) {
                    $entity->$methodName($relation);
                } else {
                    try {
                        $this->accessor->setValue($entity, $prop, $relation);
                    } catch (\TypeError $e) {
                        // @todo manage this with a log + a report to user with explanation on what have not been processed
                    }
                }

            }
        }

        return $entity;
    }

    /**
     * @todo: if json is an object : creation, if it's a string : retreive the entity with doctrine and add it to entity
     *
     * @param array $json
     * @param LibraryInterface $entity
     * @return LibraryInterface
     *
     * @throws RuntimeException
     */
    private function buildWithOneRelProps(array $json, LibraryInterface $entity): LibraryInterface
    {
        $ezProps = $this->getOneRelPropsName();
        foreach ($ezProps as $prop => $operationsInfo) {
            if (!array_key_exists($prop, $json)
                || $json[$prop] === null) {
                continue;
            }

            $this->checkOperationsInfo($operationsInfo, 'getOneRelPropsName');

            $relation = $operationsInfo['converter']->initFromRequest($json[$prop], $prop);
            $relationRegistered = $this->useRegistry($relation, $operationsInfo);

            if (array_key_exists('setter', $operationsInfo)) {
                $methodName = $operationsInfo['setter'];
                $entity->$methodName($relationRegistered);
            } else {
                try {
                    $this->accessor->setValue($entity, $prop, $relationRegistered);
                } catch (\TypeError $e) {
                    // @todo manage this with a log + a report to user with explanation on what have not been processed
                }
            }
        }

        return $entity;
    }

    /**
     * @param $jsonOrArray
     * @return mixed
     * @throws ValidationException
     */
    protected function checkJsonOrArray($jsonOrArray)
    {
        $json = is_string($jsonOrArray) ? json_decode($jsonOrArray, true) : $jsonOrArray;

        // test if invalid json, should i use json_last_error ?
        if (!$json) {
            $violationList = new ConstraintViolationList();
            $violation = new ConstraintViolation(
                sprintf('jsonOrArray for %s must be string or array', end(array_values(self::$propertyPath))),
                null, [], $jsonOrArray, $this->getPropertyPath(), $jsonOrArray);
            $violationList->add($violation);
            throw new ValidationException($violationList);
        }

        return $json;
    }

    /**
     * @param $operationsInfo
     * @param $methodName
     * @throws RuntimeException
     */
    protected function checkOperationsInfo($operationsInfo, $methodName): void
    {
        if (!array_key_exists('converter', $operationsInfo)) {
            throw new RuntimeException(sprintf('Library ParamConverter::%s must return an associative array '
                . 'with the key as the Entity props name also used in HTTP Request Json node, and the value must contain '
                . 'an array with converter key, and a setter if you don\'t want to use default propertyAccess',
                $methodName)
            );
        }

        if (!is_object($operationsInfo['converter'])
            || !$operationsInfo['converter'] instanceof ConverterInterface) {
            throw new RuntimeException('converter should be an object that implements ConverterInterface');
        }
    }

    /**
     * @param $relation
     * @param $operationsInfo
     * @return mixed
     */
    protected function useRegistry($relation, $operationsInfo)
    {
        if (array_key_exists('registryKey', $operationsInfo)) {
            if (!array_key_exists($operationsInfo['registryKey'], self::$registry)) {
                self::$registry[$operationsInfo['registryKey']] = [];
            }

            $serialized = $this->serializer->serialize($relation, 'json');
            if (array_key_exists($serialized, self::$registry[$operationsInfo['registryKey']])) {
                $relation = self::$registry[$operationsInfo['registryKey']][$serialized];
            } else {
                self::$registry[$operationsInfo['registryKey']][$serialized] = $relation;
            }
        }

        return $relation;
    }

    /**
     * @param $id
     * @param $class
     * @return null|object
     * @throws InvalidArgumentException
     */
    protected function getFromDatabase($id, $class = null)
    {
        if (!$class && static::RELATED_ENTITY) {
            $class = static::RELATED_ENTITY;
        } else {
            throw new \InvalidArgumentException(sprintf('You must define constant RELATED_ENTITY form you ParamConverter %s', static::name));
        }

        $entityExists = $this->entityManager
            ->getRepository($class)
            ->find($id);

        if ($entityExists) {
            return $entityExists;
        }

        throw new \InvalidArgumentException(sprintf('Editor %d doesn\'t exists', $id));
    }

    /**
     * @return string
     */
    private function getPropertyPath(): string
    {
        $raw = implode('.', self::$propertyPath);

        return strtr($raw, ['.[' => '[', ]);
    }
}
