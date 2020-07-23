<?php

namespace Presis\FirmasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FirmasBundle:Default:index.html.twig', array('name' => $name));
    }
}
