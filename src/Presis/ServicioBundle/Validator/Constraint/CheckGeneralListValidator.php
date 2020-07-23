<?php


namespace Presis\ServicioBundle\Validator\Constraint;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckGeneralListValidator extends ConstraintValidator{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function validate($value, Constraint $constraint)
    {
        if ($value->getCliente()) {
            $cli=$value->getCliente();
            $listacli=$cli->getLista();
            $lista=$lista=$this->entityManager->getRepository("PresisServicioBundle:Lista")->findOneById($value->getId());
            if ($listacli){
                if (!$lista){
                    $this->context->buildViolation("El cliente seleccionado ya posee una lista de precios")
                        ->setParameter('%string%', $value)
                        ->addViolation();
                }else{
                    if ($lista<>$listacli){
                        $this->context->buildViolation("El cliente seleccionado ya posee una lista de precios")
                            ->setParameter('%string%', $value)
                            ->addViolation();
                    }
                }
            }

        }

        if ($value->getIsGeneral()) {
            $lista=$this->entityManager->getRepository("PresisServicioBundle:Lista")->findOneByIsGeneral(true);

            if ($lista && ($lista->getId()<>$value->getId())) {
                $this->context->buildViolation("Solo puede haber una lista general en el sistema.")
                    ->setParameter('%string%', $value)
                    ->addViolation();
            }
        }

    }

}