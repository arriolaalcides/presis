<?php

namespace Presis\DatosEnviosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DatosEnviosBundle:Default:index.html.twig', array('name' => $name));
    }
}
