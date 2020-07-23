<?php

namespace Presis\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PresisMenuBundle:Default:index.html.twig');
    }
}
