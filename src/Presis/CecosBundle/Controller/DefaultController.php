<?php

namespace Presis\CecosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CecosBundle:Default:index.html.twig', array('name' => $name));
    }
}
