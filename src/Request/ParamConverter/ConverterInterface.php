<?php

namespace App\Request\ParamConverter;

use App\Entity\Library\LibraryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

interface ConverterInterface extends ParamConverterInterface
{
    /**
     * @return array
     */
    public function getEzPropsName(): array;

    /**
     * @return array
     */
    public function getManyRelPropsName():array;

    /**
     * @return array
     */
    public function getOneRelPropsName():array;

    /**
     * @param $jsonOrArray
     * @param $propertyPath
     * @return mixed array|LibraryInterface
     */
    public function initFromRequest($jsonOrArray, $propertyPath);
}
