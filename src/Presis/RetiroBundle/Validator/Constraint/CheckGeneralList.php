<?php
/**
 * Created by IntelliJ IDEA.
 * User: pamtru
 * Date: 08/12/2014
 * Time: 08:51
 */

namespace Presis\RetiroBundle\Validator\Constraint;


use Symfony\Component\Validator\Constraint;

/**
 * Class CheckGeneralList
 * @package Presis\ServicioBundle\Validator\Constraint
 * @Annotation
 */
class CheckGeneralList extends Constraint{
    public $message="Solo una lista puede ser general";
    public function validatedBy()
    {
        return "retiro_checker";
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
} 