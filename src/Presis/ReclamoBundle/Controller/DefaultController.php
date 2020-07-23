<?php

namespace Presis\ReclamoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ReclamoBundle:Default:index.html.twig', array('name' => $name));
    }
}
