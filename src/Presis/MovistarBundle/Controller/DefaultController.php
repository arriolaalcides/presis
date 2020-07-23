<?php

namespace Presis\MovistarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Presis\MovistarBundle\Entity\Fabricante;
use Presis\MovistarBundle\Entity\FabricanteRepository;

use Presis\MovistarBundle\Entity\Modelo;
use Presis\MovistarBundle\Entity\ModeloRepository;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MovistarBundle:Default:index.html.twig', array('name' => $name));
    }

    public function selectAction(Request $request){

        $fabricante_id = $request->get("fabricante_id");

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $dataSuc = $em->getRepository('MovistarBundle:Modelo')->findBy(array('fabricante'=>$fabricante_id, 'activo' => '1'));

        //die($dataSuc->getDescripcion());

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($dataSuc, "json");

        return new Response($json);

        /*$em = $this->getDoctrine()->getManager();
        $q = $em->getRepository("MovistarBundle:Fabricante")->findModeloByFabricante($fabricante_id);
        $modelos = $q->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize($modelos, 'json');
        return new Response($data);*/

    }

    public function findvdAction(Request $request){

        $modelo_id = $request->get("modelo_id");


        $em = $this->getDoctrine()->getManager();

        $vd = $em->getRepository('MovistarBundle:Modelo')->findOneBy(array('id'=>$modelo_id));

        return new Response($vd->getValorDeclarado());
    }
}
