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
use Presis\ServicioBundle\Entity\Precio;
use Presis\ServicioBundle\Entity\Servicio;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPrecioData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
       $precio=new Precio();
       $precio->setCordonEntrega($this->getReference("cordon1"));
       $precio->setCordonRetiro($this->getReference("cordon2"));
        $precio->setLista($this->getReference("lista"));
        $precio->setPrecio(47.84);
        $precio->setServicio($this->getReference("servicio"));
        $precio->setRango(2);
        $precio2=new Precio();
        $precio2->setCordonEntrega($this->getReference("cordon1"));
        $precio2->setCordonRetiro($this->getReference("cordon2"));
        $precio2->setLista($this->getReference("lista"));
        $precio2->setPrecio(55.36);
        $precio2->setServicio($this->getReference("servicio"));
        $precio2->setRango(10);
        $precio3=new Precio();
        $precio3->setCordonEntrega($this->getReference("cordon1"));
        $precio3->setCordonRetiro($this->getReference("cordon2"));
        $precio3->setLista($this->getReference("lista"));
        $precio3->setPrecio(75.36);
        $precio3->setServicio($this->getReference("servicio"));
        $precio3->setRango(25);
        $precio4=new Precio();
        $precio4->setCordonEntrega($this->getReference("cordon1"));
        $precio4->setCordonRetiro($this->getReference("cordon2"));
        $precio4->setLista($this->getReference("lista2"));
        $precio4->setPrecio(25);
        $precio4->setServicio($this->getReference("servicio"));
        $precio4->setRango(2);
        $precio5=new Precio();
        $precio5->setCordonEntrega($this->getReference("cordon1"));
        $precio5->setCordonRetiro($this->getReference("cordon2"));
        $precio5->setLista($this->getReference("lista2"));
        $precio5->setPrecio(28);
        $precio5->setServicio($this->getReference("servicio"));
        $precio5->setRango(10);
        $precio6=new Precio();
        $precio6->setCordonEntrega($this->getReference("cordon1"));
        $precio6->setCordonRetiro($this->getReference("cordon2"));
        $precio6->setLista($this->getReference("lista2"));
        $precio6->setPrecio(30);
        $precio6->setServicio($this->getReference("servicio"));
        $precio6->setRango(25);
        $manager->persist($precio);
        $manager->persist($precio2);
        $manager->persist($precio3);
        $manager->persist($precio4);
        $manager->persist($precio5);
        $manager->persist($precio6);
        $manager->flush();
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8; // the order in which fixtures will be loaded
    }
}