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
use Presis\ServicioBundle\Entity\Cordon;
use Presis\ServicioBundle\Entity\CpCordon;
use Presis\ServicioBundle\Entity\Servicio;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCordonData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $cordon=new Cordon();
        $cordon->setDescripcion("Capital");


        $cordon2=new Cordon();
        $cordon2->setDescripcion("Provincia");

        $cpCordon1=new CpCordon();
        $cpCordon1->setBarrio("San Cristobal");
        $cpCordon1->setCordon($cordon);
        $cpCordon1->setCp(1200);
        $cpCordon1->setLocalidad("Capital");
        $cpCordon1->setPartido("CABA");
        $cpCordon1->setPrestador("Motonorte");
        $cpCordon1->setProvincia("Capital");


        $manager->persist($cordon);
        $manager->persist($cordon2);
        $manager->persist($cpCordon1);
        $manager->flush();
        $this->addReference('cordon1', $cordon);
        $this->addReference('cordon2', $cordon2);
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }

}