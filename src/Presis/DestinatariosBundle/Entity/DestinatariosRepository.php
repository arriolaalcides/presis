<?php

namespace Presis\DestinatariosBundle\Entity;

/**
 * DestinatariosRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DestinatariosRepository extends \Doctrine\ORM\EntityRepository
{

    public function findDestiByCliente($codcli){
        $consulta=$this->createQueryBuilder('d');
        $consulta->where('d.cliente = :codcli');
        $consulta->setParameter('codcli',$codcli);
        return $consulta;
    }



}
