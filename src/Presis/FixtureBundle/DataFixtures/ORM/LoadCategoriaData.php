<?php
/**
 * Created by pamtru.
 * User: pamtru
 * Date: 19/09/2014
 * Time: 07:24 PM
 */

namespace Presis\FixtureBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Presis\GeneralBundle\Entity\Categoria;
use Presis\ServicioBundle\Entity\Cordon;
use Presis\ServicioBundle\Entity\Servicio;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadCategoriaData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $categoria=new Categoria();
        $categoria->setNombre("Bijou");
        $categoria->setPeso(0.001);
      //  $categoria->getClientes()->add($this->getReference("cliente"));

        $cliente=$this->getReference("cliente");
        $cliente->getCategorias()->add($categoria);
        $manager->persist($categoria);
        $manager->persist($cliente);
        $manager->flush();


    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 9; // the order in which fixtures will be loaded
    }

}