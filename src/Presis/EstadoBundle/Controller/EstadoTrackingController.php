<?php

namespace Presis\EstadoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\EstadoBundle\Entity\Estado;
use Presis\EstadoBundle\Form\EstadoType;
use Symfony\Component\HttpFoundation\Response;

/**
 * EstadoTracking controller.
 *
 */
class EstadoTrackingController extends Controller
{

    /**
     * Shows a change state asynchronous form and lists all the changed statuses.
     *
     */
    public function indexAction()
    {

        //$this->createFormBuilder()->add('estado');
        $estados = ''; //As EntityType

        return $this->render('EstadoBundle:EstadoTracking:index.html.twig', array(
            'estados_select' => $estados,
        ));
    }
}
