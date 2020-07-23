<?php

namespace Presis\ConstanciaRetiroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ConstanciaRetiroBundle:Default:index.html.twig', array('name' => $name));
    }
}
