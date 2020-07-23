<?php

namespace Presis\TipoDNIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TipoDNIBundle:Default:index.html.twig', array('name' => $name));
    }
}
