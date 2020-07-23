<?php

namespace Presis\ApiBundle\Controller;


use FOS\RestBundle\View\View;

use Presis\GeneralBundle\Entity\Sucursal;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ServicioController extends Controller
{
/**
    * @RequestParam(name="username",nullable=false, description="Nombre de usuario")
    * @RequestParam(name="password",nullable=false, description="Contraseña asociada al usuario")
    * @RequestParam(name="sucursal",nullable=false, description="Codigo de sucursal de la cual se va a realizar el retiro")
    * @RequestParam(name="cp",nullable=false,requirements="\d+", description="Cp del lugar donde se realizará la entrega")

     * @ApiDoc(
     *     statusCodes={
     *         200="Devuelto cuando la solicitud fue procesada correctamente.",
     *         400="Devuelto cuando hay algun problema con los parametros.",
     *         500="Devuelto cuando no se encuentra al usuario."
     *     },
     *  description="Devuelve los servicios habilitados",
     *  output="Presis\ServicioBundle\Servicio",
     *
     *  parameters={
     *         {"name"="password","dataType"="string",}
 * }
     * )
     * @param ParamFetcher $paramFetcher
     * @return FOSView
*/
    public function postServicioAction(ParamFetcher $paramFetcher=null){
        $usuario = $paramFetcher->get('username');
        $contra = $paramFetcher->get('password');
        $codsuc = $paramFetcher->get("sucursal");
        $cp= $paramFetcher->get("cp");
        $view=View::create();
        $view->setStatusCode(200);
        $repository = $this->getDoctrine()->getRepository('PresisServicioBundle:Servicio');
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $userManager = $this->get('fos_user.user_manager');

        $encoder_service = $this->get('security.encoder_factory');

        $user = $userManager->findUserByUsername($usuario);

        $encoder = $encoder_service->getEncoder($user);
        $encoded_pass = $encoder->encodePassword($contra, $user->getSalt());

        if (!isset($codsuc)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con la sucursal"));
            return $view;
        }

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
        $cp = $request->get("cp");
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
        }



    }
}
