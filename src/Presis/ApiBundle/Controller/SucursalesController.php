<?php

namespace Presis\ApiBundle\Controller;


use FOS\RestBundle\View\View;

use Presis\GeneralBundle\Entity\Sucursal;
use Presis\GeneralBundle\Entity\Cliente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class SucursalesController extends Controller
{
/**
    * @RequestParam(name="username",nullable=false, description="Nombre de usuario")
    * @RequestParam(name="password",nullable=false, description="Contraseña asociada al usuario")
    * @RequestParam(name="cliente_id",nullable=true, description="Codigo de cliente proporcionado por el sistema")
    * @RequestParam(name="razonSocial",nullable=true, description="Nombre de la empresa")
    * @RequestParam(name="cuit",nullable=true, description="C.U.I.T. de la empresa")
    * @RequestParam(name="contacto",nullable=true, description="Referente de la empresa")
    * @RequestParam(name="celular",nullable=true, description="Celular de contacto")
    * @RequestParam(name="email",nullable=true,description="Email de contacto")
    * @RequestParam(name="calle",nullable=true, description="Calle del lugar de retiro")
    * @RequestParam(name="altura",nullable=true, description="Altura del lugar de retiro")
    * @RequestParam(name="piso",nullable=true,description="Piso del lugar de retiro")
    * @RequestParam(name="depto",nullable=true,description="Depto del lugar de retiro")
    * @RequestParam(name="localidad",nullable=true,description="Localidad del lugar de retiro")
    * @RequestParam(name="provincia",nullable=true,description="Provincia del lugar de retiro")
    * @RequestParam(name="cp",nullable=true,description="Cp del lugar donde se realizará la entrega")
    * @RequestParam(name="kms",nullable=true,description="Kms desde origen hasta lugar de retiro")
    * @RequestParam(name="obs",nullable=true,description="Observaciones")
    * @RequestParam(name="descripcion",nullable=true,description="Descripción de la sucursal")

     * @ApiDoc(
     *     statusCodes={
     *         200="Devuelto cuando la solicitud fue procesada correctamente.",
     *         400="Devuelto cuando hay algun problema con los parametros.",
     *         500="Devuelto cuando no se encuentra al usuario."
     *     },
     *  description="Agrega una sucursal a un cliente",
     *  output="Presis\GeneralBundleBundle\Sucursal",
     *
     *  parameters={
     *         {"name"="password","dataType"="string",}
 * }
     * )
     * @param ParamFetcher $paramFetcher
     * @return FOSView
*/

/*
 * http://trackers.onlinegeotrack.com.ar/desarrollo/epresis/web/api/v1/sucursals.json
 * {
      "username": "naima",
      "password": "703011",
      "cliente_id": "189",
      "razonSocial": "una sucursal de prueba",
      "cp": "1247",
      "descripcion": "primer sucursal de api",
      "calle": "lerma",
      "altura": "667",
      "piso": "2",
      "depto": "A",
      "localidad": "Villa Tesei",
      "provincia": "Buenos Aires"
    }

 */
    public function postSucursalAction(ParamFetcher $paramFetcher=null){


        $usuario = $paramFetcher->get('username');
        $contra = $paramFetcher->get('password');

        $cliente_id = $paramFetcher->get('cliente_id');
        $cp = $paramFetcher->get('cp');
        $razonSocial = $paramFetcher->get('razonSocial');
        $calle = $paramFetcher->get('calle');
        $altura = $paramFetcher->get('altura');
        $piso = $paramFetcher->get('piso');
        $depto = $paramFetcher->get('depto');
        $descripcion = $paramFetcher->get('descripcion');
        $localidad = $paramFetcher->get('localidad');
        $provincia = $paramFetcher->get('provincia');


        $view=View::create();
        $view->setStatusCode(200);

        //$manager=$this->getDoctrine()->getManager();

        $em = $this->getDoctrine()->getManager();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $userManager = $this->get('fos_user.user_manager');

        $encoder_service = $this->get('security.encoder_factory');

        $user = $userManager->findUserByUsername($usuario);

        $encoder = $encoder_service->getEncoder($user);
        $encoded_pass = $encoder->encodePassword($contra, $user->getSalt());

        if ($user) {
            if (!($user->getPassword() == $encoded_pass)) {
                $view->setStatusCode(400);
                $view->setData(Array("message"=>"Ha ocurrido un error con el usuario o contraseña"));
                return $view;
            }
        }else{
            $view->setStatusCode(400);
            $view->setData(Array("message"=>"Ha ocurrido un error con el usuario o contraseña"));
            return $view;
        }

        $query = $em->createQuery('SELECT MAX(m.id) AS id FROM PresisGeneralBundle:Sucursal m ')
            ->setMaxResults(1)
            ->getOneOrNullResult();

        $cliente = $this->getDoctrine()->getRepository("PresisGeneralBundle:Cliente")->findOneBy(
            array('id' => $cliente_id));

        $sucursal = new Sucursal();
        $sucursal->setCliente($cliente);
        $sucursal->setRazonSocial($razonSocial);
        $sucursal->setCalle($calle);
        $sucursal->setAltura($altura);
        $sucursal->setPiso($piso);
        $sucursal->setDpto($depto);
        $sucursal->setLocalidad($localidad);
        $sucursal->setProvincia($provincia);
        $sucursal->setCp($cp);
        $sucursal->setDescripcion($descripcion);
        $sucursal->setCodSuc($query['id']+1);

        $em->persist($sucursal);
        $em->flush();

        $view->setData(Array("status"=>200,"sucursal"=>$sucursal->getId()));
        return $view;
        /*$cp = $request->get("cp");
        if (!isset($cp)){
            $view->setStatusCode(400);
            $view->setData(Array("message"=>"Ha ocurrido un error con el cp"));
            return $view;
        }
        $sucrepo=$this->getDoctrine()->getRepository("PresisGeneralBundle:Sucursal");
        $sucu=$sucrepo->findOneByCodSuc($codsuc);

        if ($sucu) {
            $servicios = $repository->findHabilitados($user, $cp,$sucu->getId())->getQuery()->getResult();
            $view->setStatusCode(200);
            $view->setData($servicios);
            return $view;
        }else{
            $view->setStatusCode(400);
            $view->setData(Array("message"=>"Ha ocurrido un error con la sucursal"));
            return $view;
        }*/
    }
}
