<?php

namespace Presis\ApiBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Security\Acl\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PrecioController extends Controller
{
    protected function getPrecioManager()
    {
        return $this->container->get('presis_precio');
    }
    /**
     * @RequestParam(name="username",nullable=false, description="Nombre de usuario")
     * @RequestParam(name="password",nullable=false, description="Contraseña asociada al usuario")
     * @RequestParam(name="sucursal",nullable=false, description="Codigo de sucursal de la cual se va a realizar el retiro")
     * @RequestParam(name="servicio",nullable=false, description="Codigo del servicio solicitado para el retiro")
     * @RequestParam(name="productos",nullable=false,array=true,description="Array json de productos con su datos de tipo de carga, su peso, categoria o dimensiones")
     * @RequestParam(name="cp",nullable=false,requirements="\d+", description="Cp del lugar donde se realizará la entrega")

     * @ApiDoc(
     *     statusCodes={
     *         200="Devuelto cuando la solicitud fue procesada correctamente.",
     *         400="Devuelto cuando hay algun problema con los parametros.",
     *         500="Devuelto cuando no se encuentra al usuario."
     *     },
     *  description="Devuelve el precio del servicio solicitado",
     *
     *  parameters={
     *         {"name"="password","dataType"="string",}
     * }
     * )
     * @param ParamFetcher $paramFetcher
     * @return FOSView
     */

    public function postPrecioAction(ParamFetcher $paramFetcher=null)
    {
        $usuario = $paramFetcher->get('username');
        $contra = $paramFetcher->get('password');
        $codsuc = $paramFetcher->get("sucursal");
        $productos = $paramFetcher->get("productos");
        $cp= $paramFetcher->get("cp");
        $cs =$paramFetcher->get("servicio");

        $view = View::create();
        $view->setStatusCode(200);
        $repository = $this->getDoctrine()->getRepository('PresisServicioBundle:Servicio');

        $totpeso = 0;
      //  $request = $this->container->get('request_stack')->getCurrentRequest();
        $userManager = $this->get('fos_user.user_manager');


        $encoder_service = $this->get('security.encoder_factory');

           $user = $userManager->findUserByUsername($usuario);

           $encoder = $encoder_service->getEncoder($user);
        $encoded_pass = $encoder->encodePassword($contra, $user->getSalt());


        if ($user) {
            if (!($user->getPassword() == $encoded_pass)) {
                $view->setStatusCode(400);
                $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el usuario o contraseña"));
                return $view;
            }
        }else{
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el usuario o contraseña"));
            return $view;
        }

        if (!isset($codsuc)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con la sucursal"));
            return $view;
        }
        $sucu=$this->getPrecioManager()->validarSucursal($user->getCliente(),$codsuc);

        if (!($sucu)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con la sucursal"));
            return $view;
        }
        if (!isset($cs)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el servicio"));
            return $view;
        }
        if (!isset($cp)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el cp"));
            return $view;
        }
        $cli=$user->getCliente();
        $servicio=$this->getPrecioManager()->validarServicio($user,$cp,$codsuc,$cs);
        if (!$servicio){
            $view->setStatusCode(404);
            $view->setData(Array("status"=>404,"message"=>"Codigo de servicio incorrecto o no habilitado"));
            return $view;
        }
        foreach ($productos as $producto){
            if ($producto["tipo"]==1)
            {

                if (!isset($producto["dimensiones"]) || !$this->getPrecioManager()->validarDimensiones($producto["dimensiones"])){
                    $view->setStatusCode(400);
                    $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con las dimensiones"));
                    return $view;
                }
                $peso=$this->getPrecioManager()->calcularPesoVolumetrico($producto["dimensiones"][0]["alto"],$producto["dimensiones"][0]["largo"],$producto["dimensiones"][0]["profundidad"],$cli->getAforo());
                $totpeso+=$peso;
            }
            if ($producto["tipo"]==2)
            {
                if (!isset($producto["peso"]) || !$this->getPrecioManager()->validarPeso($producto["peso"])){
                    $view->setStatusCode(400);
                    $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el peso"));
                    return $view;
                }
                $totpeso+=$producto["peso"];
            }
            if ($producto["tipo"]==3)
            {
                $categoria=$producto["categoria"];
                if (!isset($categoria) || !$this->getPrecioManager()->validarCategoria($categoria,$user->getCliente())){
                    $view->setStatusCode(400);
                    $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con la categoria"));
                    return $view;
                }

                $totpeso+=$this->getPrecioManager()->getPesoByCategoria($categoria);
            }
        }
        $precio=$this->getPrecioManager()->calcularPrecio($servicio,$totpeso,$user->getCliente(),$sucu,$cp);

        if ($precio){
            $view->setStatusCode(200);

            $view->setData(Array("moneda"=>"$","precio"=>$precio->getPrecio()));
        }else{
            $view->setStatusCode(400);
            $view->setData(Array("message"=>"Ha ocurrido un error al calcular el precio"));

        }

        return $view;


    }





}
