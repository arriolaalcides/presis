<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SucursalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SucursalRepository extends EntityRepository
{

    public function findSucursalByCliente($codcli){

        $consulta=$this->createQueryBuilder('c');
        $consulta->where('c.cliente = :codcli');
        $consulta->setParameter('codcli',$codcli);
        return $consulta;
    }

}
