<?php
/**
 * Created by pamtru.
 * User: pamtru
 * Date: 19/09/2014
 * Time: 07:24 PM
 */

namespace Presis\FixtureBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Presis\ServicioBundle\Entity\Servicio;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadServicioData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $servicio = new Servicio();
        $servicio->setCodServ("48");
        $servicio->setDescripcion("Servicio 48hs");
        $manager->persist($servicio);
        $manager->flush();
        $this->addReference('servicio', $servicio);
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}