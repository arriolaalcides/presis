<?php
/**
 * Created by pamtru.
 * User: pamtru
 * Date: 19/09/2014
 * Time: 07:24 PM
 */

namespace Presis\FixtureBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Presis\ServicioBundle\Entity\Habilitaciones;
use Presis\ServicioBundle\Entity\Servicio;


class LoadHabilitacionData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $habilitacion=new Habilitaciones();
        $habilitacion->setCordonEntrega($this->getReference("cordon1"));
        $habilitacion->setCordonRetiro($this->getReference("cordon2"));
        $habilitacion->setServicio($this->getReference("servicio"));
        $horad=new \DateTime("00:00:00");
        $horah=new \DateTime("23:59:59");
        $habilitacion->setHoraDesde($horad);
        $habilitacion->setHoraHasta($horah);

        //$this->addReference('servicio', $servicio);
        $habilitacion2=new Habilitaciones();
        $habilitacion2->setCordonEntrega($this->getReference("cordon2"));
        $habilitacion2->setCordonRetiro($this->getReference("cordon2"));
        $habilitacion2->setServicio($this->getReference("servicio"));
        $habilitacion2->setHoraDesde($horad);
        $habilitacion2->setHoraHasta($horah);
        $manager->persist($habilitacion);
        $manager->persist($habilitacion2);
        $manager->flush();

    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}