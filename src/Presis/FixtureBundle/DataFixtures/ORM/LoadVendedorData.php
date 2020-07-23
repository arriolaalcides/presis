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
use Presis\GeneralBundle\Entity\Sucursal;
use Presis\GeneralBundle\Entity\Vendedor;
use Presis\ServicioBundle\Entity\Servicio;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadVendedorData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $vendedor= new Vendedor();
        $vendedor->setNombre("Juann");
        $this->setReference("vendedor",$vendedor);

        $manager->persist($vendedor);
        $manager->flush();

    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}