<?php

namespace Presis\ExpresoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ExpresoBundle:Default:index.html.twig', array('name' => $name));
    }
}
