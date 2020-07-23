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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface,ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $userAdmin = $userManager->createUser();

        $userAdmin->setUsername('System');
        $userAdmin->setEmail('system@example.com');
        $userAdmin->setPlainPassword('test');
        $userAdmin->setEnabled(true);
        $userAdmin->addRole("ROLE_ADMIN");
        $userManager->updateUser($userAdmin, true);
        $this->setReference("user",$userAdmin);
       // $userManager = $this->container->get('fos_user.user_manager');
        $user=$userManager->createUser();
        $user->setUsername("pamtru");
        $user->setPlainPassword("pamtru");
        $user->addRole("ROLE_VENDEDOR");
        $user->setVendedor($this->getReference("vendedor"));
        $user->setEnabled(true);
        $user->setEmail("pamtru@gmail.com");
        $userManager->updateUser($user, true);
    }
    public function getOrder()
    {
        return 2;
    }
}