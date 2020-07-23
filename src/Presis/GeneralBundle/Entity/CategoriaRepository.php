<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CategoriaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoriaRepository extends EntityRepository
{
    public function findCategoriaByCliente($cliente,$categoria){
        $em=$this->getEntityManager();
        $dql='SELECT cat
                from PresisGeneralBundle:Categoria cat
                join c.$clientes cli';
        $consulta=$em->createQuery($dql);

        return $consulta->getResult();


    }
}