<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class IsMacValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
      if($value <> null)
      {
        $prepared = strtolower(str_replace(":","",str_replace("-", "", trim($value)))); 
        if(strlen($prepared) <> 12  || !preg_match('/^[0-9abcdef]{12}$/', $prepared))
        {
           $this->context->addViolation(
                 $constraint->message,
                 array('%string%' => $prepared));

        }
      } 
    }
}