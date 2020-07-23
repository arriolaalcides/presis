<?php

namespace Presis\ServicioBundle\Validator\Constraint;
use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 */
class CheckTime extends Constraint{
    public $message = 'Max time must be greater than min time';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

} 