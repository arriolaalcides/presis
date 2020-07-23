<?php

namespace Presis\ServicioBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CordonRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CordonRepository extends EntityRepository
{
    public function findCordonInCp($cp){
        $em=$this->getEntityManager();
        $dql='SELECT c
                from PresisServicioBundle:Cordon c
                where c.cpDesde<=:cp
                and c.cpHasta>=:cp';
        $consulta=$em->createQuery($dql);
        $consulta->setParameter("cp",$cp);
        return $consulta->getSingleResult();


    }
}
