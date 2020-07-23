<?php

namespace Presis\GeneralBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * PesoRangoExcedenteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PesoRangoExcedenteRepository extends \Doctrine\ORM\EntityRepository
{

    public function getPrecios($cliente){
        $query=$this->createQueryBuilder('a')
            ->where('a.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->getQuery()->getResult();
        //return $query->getArrayResult();
        return new ArrayCollection($query);
    }

}