<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class HasOneOfUserOrNameNotNullValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof HasOneOfUserOrNameNotNull) {
            throw new UnexpectedTypeException($constraint, HasOneOfUserOrNameNotNull::class);
        }

        if (null === $value->getLastname()
            && null === $value->getUserId()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('UserId')
                ->addViolation();
        }
    }
}
