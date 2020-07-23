<?php
/**
 * Created by IntelliJ IDEA.
 * User: pamtru
 * Date: 20/10/2014
 * Time: 02:26 PM
 */

namespace Presis\ServicioBundle\Validator\Constraint;


use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
class CheckTimeValidator extends ConstraintValidator{
    public function validate($habilitacion, Constraint $constraint)
    {
        if ($habilitacion->getHoraDesde() > $habilitacion->getHoraHasta()) {
            $this->context->buildViolation('La hora desde no puede ser mayor a la hasta')
                ->addViolation();
            ;


        }
    }
} 