<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HasOneOfUserOrNameNotNull extends Constraint
{
    public $message = 'The entity must contain a valid UserID or a valid lastname.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}
