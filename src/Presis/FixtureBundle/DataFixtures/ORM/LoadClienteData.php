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
use Presis\GeneralBundle\Entity\Cliente;
use Presis\ServicioBundle\Entity\Servicio;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadClienteData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $cliente=new Cliente();
        $cliente->setContacto("Juan perez");
        $cliente->setEmpresa("test empresa");
        $cliente->setVendedor($this->getReference("vendedor"));
        $cliente->setCustomPriceList(true);
        $usuario=$this->getReference("user");

        $manager->persist($cliente);

        $manager->flush();
        $usuario->setCliente($cliente);
        $manager->persist($usuario);
        $manager->flush();
        $this->setReference("cliente",$cliente);
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5; // the order in which fixtures will be loaded
    }
}