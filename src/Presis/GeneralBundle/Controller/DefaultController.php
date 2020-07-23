<?php

namespace Presis\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction($name)
    {
        return $this->render('PresisGeneralBundle:Default:index.html.twig', array('name' => $name));
    }
}
