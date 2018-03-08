<?php

namespace App\Request\ParamConverter;

use App\Entity\Library\LibraryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

interface ConverterInterface extends ParamConverterInterface
{
    /**
     * @return array
     */
    function getEzPropsName(): array;

    /**
     * @return array
     */
    function getManyRelPropsName():array;

    /**
     * @return array
     */
    function getOneRelPropsName():array;

    /**
     * @param $jsonOrArray
     * @param $propertyPath
     * @return mixed array|LibraryInterface
     */
    function initFromRequest($jsonOrArray, $propertyPath);
}
