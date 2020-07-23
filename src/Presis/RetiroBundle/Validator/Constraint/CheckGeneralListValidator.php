<?php


namespace Presis\RetiroBundle\Validator\Constraint;


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




    }

}