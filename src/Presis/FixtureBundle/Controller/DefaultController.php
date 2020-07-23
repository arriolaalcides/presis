<?php

namespace Presis\FixtureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PresisFixtureBundle:Default:index.html.twig', array('name' => $name));
    }
}
