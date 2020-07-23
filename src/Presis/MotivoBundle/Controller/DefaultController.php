<?php

namespace Presis\MotivoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NotivoBundle:Default:index.html.twig', array('name' => $name));
    }
}
