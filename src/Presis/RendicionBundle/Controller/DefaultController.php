<?php

namespace Presis\RendicionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RendicionBundle:Default:index.html.twig', array('name' => $name));
    }
}
