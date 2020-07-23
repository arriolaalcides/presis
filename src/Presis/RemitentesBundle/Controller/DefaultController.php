<?php

namespace Presis\RemitentesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RemitentesBundle:Default:index.html.twig', array('name' => $name));
    }
}
