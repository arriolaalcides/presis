<?php

namespace Presis\DestinatariosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DestinatariosBundle:Default:index.html.twig', array('name' => $name));
    }
}
