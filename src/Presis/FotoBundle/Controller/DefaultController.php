<?php

namespace Presis\FotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FotoBundle:Default:index.html.twig', array('name' => $name));
    }
}
