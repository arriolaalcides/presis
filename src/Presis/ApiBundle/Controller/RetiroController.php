<?php

namespace Presis\ApiBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\View\View;

use Presis\GeneralBundle\Entity\Sucursal;
use Presis\RetiroBundle\Entity\Comprador;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\RetiroBundle\Entity\Sender;
use Presis\TrackerBundle\DependencyInjection\TrackerExtension;
use Presis\TrackerBundle\Entity\Tracker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Security\Acl\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class RetiroController extends Controller
{

    private $SABADO = 6;

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
     * @RequestParam(name="comprador",nullable=false,array=true,description="Datos de la entrega, destinatario,calle,altura,cp")
     * @RequestParam(name="fragil",nullable=true,description="Indica si el retiro posee productos fragiles")
     * @RequestParam(name="franja",nullable=false,description="Franja de entrega")
     * @RequestParam(name="bultos",nullable=true,description="Cantidad de paquetes por guia")
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

    public function postRetiroAction(ParamFetcher $paramFetcher=null)
    {
        $usuario = $paramFetcher->get('username');
        $contra = $paramFetcher->get('password');
        $codsuc = $paramFetcher->get("sucursal");

        $comprador=$paramFetcher->get("comprador")[0];
        $productos = $paramFetcher->get("productos");
        $cs =$paramFetcher->get("servicio");
        $fragil=$paramFetcher->get("fragil");
        $franjacod=$paramFetcher->get("franja");
        $bultos=$paramFetcher->get("bultos");

        $view = View::create();
        $view->setStatusCode(200);

        $manager=$this->getDoctrine()->getManager();
        $totpeso = 0;

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
        if (isset($fragil)){
            if ($fragil<>0 && $fragil<>1){
                $view->setStatusCode(400);
                $view->setData(Array("status"=>400,"message"=>"Fragil debe ser 0 o 1"));
                return $view;
            }
        }
        if (!isset($codsuc)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con la sucursal"));
            return $view;
        }

        $sucursal=$this->getPrecioManager()->validarSucursal($user->getCliente(),$codsuc);
        if (!($sucursal)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con la sucursal"));
            return $view;
        }

        if (!isset($comprador) || !$this->getPrecioManager()->validarComprador($comprador)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con los datos del comprador"));
            return $view;
        }

        if (!isset($productos)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con los datos de los productos"));
            return $view;
        }
        $cp=$comprador["cp"];
        if (!isset($cp)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el cp"));
            return $view;
        }
        $email=$comprador["email"];
        if (!isset($email)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el email"));
            return $view;
        }
        $celular=$comprador["celular"];
        if (!isset($celular)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el celular"));
            return $view;
        }
        $servicio=$this->getPrecioManager()->validarServicio($user,$cp,$codsuc,$cs);
        if (!$servicio){
            $view->setStatusCode(404);
            $view->setData(Array("status"=>404,"message"=>"Codigo de servicio incorrecto o inhabilitado"));
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
                $peso=$this->getPrecioManager()->calcularPesoVolumetrico($producto["dimensiones"][0]["alto"],$producto["dimensiones"][0]["largo"],$producto["dimensiones"][0]["profundidad"],$user->getCliente()->getAforo());
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
                $totpeso+=$this->getPrecioManager()->getPesoByCategoria($categoria,$user->getCliente());
            }
            if ($producto["tipo"]==4)
            {

                if ((!isset($producto["dimensiones"]) || !$this->getPrecioManager()->validarDimensiones($producto["dimensiones"])) && (!isset($producto["peso"]) || !$this->getPrecioManager()->validarPeso($producto["peso"]))){
                    $view->setStatusCode(400);
                    $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el peso o las dimensiones dimensiones"));
                    return $view;
                }
                $peso=$this->getPrecioManager()->calcularMayorPeso($producto["dimensiones"][0]["alto"],$producto["dimensiones"][0]["largo"],$producto["dimensiones"][0]["profundidad"],$producto["peso"],$user->getCliente()->getAforo());
                $totpeso+=$peso;
            }
        }
        $franja=$this->getPrecioManager()->validarFranja($franjacod);
        if (!($franja)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con la franja"));
            return $view;
        }
        $precio=$this->getPrecioManager()->calcularPrecio($servicio,$totpeso,$user->getCliente(),$sucursal,$cp);
        if(!isset($precio)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"No se encontro el precio."));
            return $view;
        }
        if($precio=="-1"){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"No se encontro el precio."));
            return $view;
        }
        if (!($precio)){
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error al calular el precio"));
            return $view;
        }
        //   $sucursal=$this->getSucursal($user->getCliente(),$codsuc);
        $retiro=new Retiro();

        $retiro->setComprador($this->setCompradorData($comprador));
        $retiro->setCliente($user->getCliente());
        $retiro->setFechHora(new \DateTime('now'));
        $retiro->setRango($precio->getRango());
        $retiro->setPrecio($precio->getPrecio());
        $retiro->setSucursal($sucursal);
        $retiro->setServicio($servicio);
        $retiro->getDatosEnvios()->setBultos($bultos);
        $retiro->getDatosEnvios()->setPeso($totpeso);

        //AGREGADO POR PICCINI PARA QUE GUARDE EL ESTADO - DEBE RETIRARSE EN 0 Y CONFIRMADA EN 1
        $repoestado = $this->getDoctrine()->getRepository('EstadoBundle:Estado');
        $estado=$repoestado->find(106);
        $retiro->setEstado($estado);
        $retiro->getDatosEnvios()->setCliente($user->getCliente());
        $retiro->getDatosEnvios()->setDebeRetirarse(true);
        $retiro->getDatosEnvios()->setConfirmada(true);
        $retiro->getDatosEnvios()->setFechaConfirmada(new \DateTime());
        /*=========================================================================================*/

        $retiro->setSender($this->setSenderData($sucursal));
        $retiro->setFranja($franja);
        $retiro->setPeso($totpeso);
        if (isset($fragil)){
            $retiro->setFragil($fragil);
        }


        $this->calcularTiempoEnTransito($retiro);

        $repoCordon = $this->getDoctrine()->getRepository('PresisServicioBundle:CpCordon');
        $cpcordon=$repoCordon->findOneByCp($cp);

        $retiro->setCordonEntrega($cpcordon->getCordon());
        $retiro->setPrestador($this->getPrecioManager()->generatePrestador($cpcordon));


        $cpCordonEntrega = $this->getDoctrine()->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $cp));

        $cpCordonOrigen = $this->getDoctrine()->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $sucursal->getCp()));

        $retiro->setZonaOrigen($cpCordonOrigen->getZona());
        $retiro->setZona($cpCordonEntrega->getZona());

        $retiro->getDatosEnvios()->setCordonOrigen($cpCordonOrigen->getCordon());
        $retiro->getDatosEnvios()->setCordonDestino($cpCordonEntrega->getCordon());

        $tracker = new Tracker();
        $tracker->setRetiro($retiro);
        $tracker->setEstado($estado);
        $tracker->setUser($user);
        $tracker->setDetalles("INGRESO API");

        $manager->persist($tracker);
        $manager->persist($retiro);
        $manager->flush();

        $view->setData(Array("status"=>200,"tracking"=>$retiro->getId()));
        return $view;

    }

    public function setCompradorData($comprador){
        $com=new Comprador();
        $com->setCalle($comprador["calle"]);
        $com->setCp($comprador["cp"]);
        $com->setAltura($comprador["altura"]);
        $com->setApenom($comprador["destinatario"]);
        $com->setEmail($comprador["email"]);
        $com->setCelular($comprador["celular"]);
        $com->setLocalidad($comprador["localidad"]);
        $com->setProvincia($comprador["provincia"]);
        if (isset($comprador["dpto"])) {
            $com->setDpto($comprador["dpto"]);
        }
        if (isset($comprador["piso"])) {
            $com->setPiso($comprador["piso"]);
        }
        if (isset($comprador["otra_info"])) {
            $com->setOtherInfo($comprador["otra_info"]);
        }
        return $com;
    }

    public function setSenderData($sucursal){
        $sender=new Sender();
        $sender->setAltura($sucursal->getAltura());
        $sender->setCp($sucursal->getCp());
        $sender->setCalle($sucursal->getCalle());
        $sender->setDpto($sucursal->getDpto());
        $sender->setEmpresa($sucursal->getCliente()->getEmpresa());
        $sender->setOtherInfo($sucursal->getOtherInfo());
        $sender->setLocalidad($sucursal->getDescripcion());
        $sender->setProvincia($sucursal->getOtherInfo());
        return $sender;
    }

    public function getSucursal($cli,$codsuc){
        $critsuc=Criteria::create()
            ->where(Criteria::expr()->eq("id",$codsuc))
            ->setMaxResults(1);
        $sucursal=$cli->getSucursales()->matching($critsuc)->get(0);
        return $sucursal;
    }

    /*================== FUNCIONES PARA CALCULAR EL TIEMPO EN TRANSITO O FECHA PACTADA =========================*/
    /**
     * Calcula la fechaPactada usando los CPs de origen y destino
     *
     * @param Retiro $retiro
     */
    public function calcularTiempoEnTransito(Retiro $retiro)
    {
        $cpComprador = $retiro->getComprador()->getCp();
        $cpSender = $retiro->getSender()->getCp();
        $compradorEsDelInterior = $this->esDelInterior($cpComprador);
        $senderEsDelInterior = $this->esDelInterior($cpSender);

        $tiempo = 0;
        if($compradorEsDelInterior) {
            $tiempo += $this->getTiempoDeEntrega($cpComprador);
        }
        if($senderEsDelInterior) {
            $tiempo += $this->getTiempoDeEntrega($cpSender);
        }
        if($compradorEsDelInterior && $senderEsDelInterior) {
            $tiempo += DatosEnvios::CROSS_DOCKING;
        }
        if(!$compradorEsDelInterior && !$senderEsDelInterior) {
            $tiempo += $this->getTiempoDeEntrega($cpComprador);
        }

        $days = floor($tiempo/24);
        $fecha = new \DateTime($retiro->getDatosEnvios()->getFecha()->format(\DateTime::ISO8601));
        $fecha->add(new \DateInterval('P'.$days.'D'));

        $fecha = $this->excluirFinDeSemana($retiro->getDatosEnvios()->getFecha()->format('U'), $fecha->format('U'));

        $retiro->getDatosEnvios()->setFechaPactada($fecha);
    }

    private function getTiempoDeEntrega($cp)
    {
        $em = $this->getDoctrine()->getManager();
        $cpCordon = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(array('cp' => $cp));

        return $cpCordon->getTiempoDeEntrega();
    }

    private function esDelInterior($cp)
    {
        $em = $this->getDoctrine()->getManager();
        $cpCordon = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(array('cp' => $cp));

        $cordon = $cpCordon->getCordon()->getDescripcion();
        return ($cordon != "1" && $cordon != "2");
    }


    private function excluirFinDeSemana($inicio, $final) {
        $unDia = new \DateInterval('P1D');

        $fechaInicio = new \DateTime("@$inicio");
        $fechaFinal = new \DateTime("@$final");
        if($inicio <= $final) {
            if ($fechaInicio->format('N') >= $this->SABADO) {
                $fechaFinal->add($unDia);
            }
            $fechaInicio->add($unDia);
            return $this->excluirFinDeSemana($fechaInicio->format('U'), $fechaFinal->format('U'));
        } else {
            return $fechaFinal;
        }
    }


}
