<?php

namespace Presis\DatosEnviosBundle\Entity;

/**
 * DatosEnviosRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DatosEnviosRepository extends \Doctrine\ORM\EntityRepository
{


    /*public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery(
                //'SELECT p.id FROM DatosEnviosBundle:DatosEnvios p'
            //'SELECT p FROM AcmeStoreBundle:Product p ORDER BY p.name ASC'
                'SELECT r.id
            FROM PresisRetiroBundle:Retiro r, DatosEnviosBundle:DatosEnvios d
            where r.datosEnvios=d'
            )
            ->getResult();
    }*/

}
