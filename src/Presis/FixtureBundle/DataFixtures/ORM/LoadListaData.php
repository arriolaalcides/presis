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
use Presis\ServicioBundle\Entity\Lista;
use Presis\ServicioBundle\Entity\Servicio;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadListaData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $lista=new Lista();
        $lista->setDescripcion("Lista General");
        $lista->setIsGeneral(true);

        $lista2=new Lista();
        $lista2->setDescripcion("Lista Cliente");
        $lista2->setIsGeneral(false);
        $lista2->setCliente($this->getReference("cliente"));
        $lista2->setVendedor($this->getReference("vendedor"));
        $cliente=$this->getReference("cliente");


        $manager->persist($lista);
        $manager->persist($lista2);
        $manager->flush();
        $cliente->setLista($lista2);
        $manager->persist($cliente);
        $manager->flush();
        $this->addReference("lista",$lista);
        $this->addReference("lista2",$lista2);

    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 7; // the order in which fixtures will be loaded
    }
}