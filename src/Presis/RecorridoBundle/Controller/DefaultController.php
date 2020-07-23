<?php

namespace Presis\RecorridoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RecorridoBundle:Default:index.html.twig', array('name' => $name));
    }
}
