<?php

namespace Presis\GuiaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PresisGuiaBundle:Default:index.html.twig', array('name' => $name));
    }
}
