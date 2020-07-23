<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Presis\GeneralBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Categoria', array('route' => 'categoria'));
        $menu->addChild('Lista', array('route' => 'lista'));

        // ... add more children

        return $menu;
    }
}
