<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsMac extends Constraint
{
    public $message = 'This string "%string%" is not proper MAC address.';
}