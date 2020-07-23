<?php

namespace Presis\ApiBundle\Controller;

use FOS\RestBundle\View\View;

use Presis\FirmasBundle\Entity\Firmas;
use Presis\RetiroBundle\Entity\Retiro;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Request\ParamFetcher;
use Presis\TrackerBundle\Entity\Tracker;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Security\Acl\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EntregasController extends Controller
{
    private $SABADO = 6;

    /**
     * @RequestParam(name="fecha",nullable=false, description="Nro. de retiro")
     * @RequestParam(name="tracking",nullable=true, description="Guias asociadas al retiro")
     * @RequestParam(name="apenom",nullable=true, description="Codigo de estado")
     * @RequestParam(name="dni",nullable=true, description="Descripcion del estado")
     * @RequestParam(name="obs",nullable=true,description="Fecha de informe")
     * @RequestParam(name="estado",nullable=true,description="Movil que informo")
     * @RequestParam(name="detalleEstado",nullable=true,description="Movil que informo")
     * @RequestParam(name="imagen",nullable=true,description="Movil que informo")
     * @RequestParam(name="movil_id",nullable=true,description="Movil que informo")
     * @RequestParam(name="lat",nullable=true,description="Latitud del GPS")
     * @RequestParam(name="lon",nullable=true,description="Longitud del GPS")
     * @ApiDoc(
     *     statusCodes={
     *         200="Devuelto cuando se actualiza correctamente.",
     *         400="Devuelto cuando hay algun problema con los parametros.",
     *         500="Devuelto cuando no se encuentra el retiro y/o tracking."
     *     },
     *  description="Actualiza el estado, modifica la fecha pactada y confirma una guia, agrega tracking",
     *
     *
     * )
     * @param ParamFetcher $paramFetcher
     * @return FOSView
     */

    public function postEntregaAction(ParamFetcher $paramFetcher=null)
    {
        $fecha = $paramFetcher->get('fecha');
        $tracking = $paramFetcher->get('tracking');
        $apenom = $paramFetcher->get('apenom');
        $dni = $paramFetcher->get('dni');
        $obs = $paramFetcher->get('obs');
        $estado = $paramFetcher->get('estado');
        $detalleEstado = $paramFetcher->get('detalleEstado');

        $lat = $paramFetcher->get('lat');
        $lon = $paramFetcher->get('lon');

        $imagen = base64_decode($paramFetcher->get('imagen'));
        $fileName = $tracking.'-'.time().uniqid().".jpg";

        file_put_contents('uploadfirma/'.$fileName,$imagen);

        /*if(file_put_contents('uploadfirma/'.$fileName,$imagen)){
            die("1");
        }else{
            die("2");
        }*/
        //$path = __DIR__ . '/../../../../web/updaloadfirma/'.$fileName;
        //die($path);
        $movil_id = $paramFetcher->get('movil_id');


        $view = View::create();
        $view->setStatusCode(200);

        $em = $this->getDoctrine()->getManager();


        $distribuidor = $em->getRepository('DistribuidorBundle:Distribuidor')->findOneBy(array('id'=>$movil_id));

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('id'=>$tracking));
        $repoestado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>$estado));

        $userWeb = $entities = $em->getRepository('PresisUserBundle:User')->findOneBy(array("username"=>"web"));

        $firma = new Firmas();
        $firma->setTracking($tracking);
        $firma->setImg($fileName);
        $firma->setCodEstado($estado);
        $firma->setDetalleEstado($detalleEstado);
        $firma->setRecibio($apenom);
        $firma->setDocumento($dni);
        $firma->setObs($obs);
        $firma->setFechaCel(new \DateTime($fecha));
        $firma->setFechaBase(new \DateTime());
        $firma->setDistribuidorId($movil_id);
        $firma->setDistribuidor($distribuidor->getCodigo().' - '.$distribuidor->getApellido().', '.$distribuidor->getNombre());
        $firma->setLat($lat);
        $firma->setLon($lon);

        $retiro->setEstado($repoestado);
        $retiro->setFechaUltimoEstado(new \DateTime($fecha));
        $retiro->getDatosEnvios()->setFecha(new \DateTime($fecha));
        $retiro->setFechaHoraEntrega(new \DateTime($fecha));
        $retiro->setDetalleEntrega($obs);
        $retiro->setReceptorNombre($apenom);
        $retiro->setReceptorApellido($apenom);
        $retiro->setDni($dni);
        $retiro->setDetalleEntrega($obs);

        if($retiro->getDatosEnvios()->getFechaPactada()) {
            $delay = $repoestado->getDelay();
            $newDate = $retiro->getDatosEnvios()->getFechaPactada();
            $newDate->modify('+' . $delay . ' days');
            $data = $newDate->format('Y-m-d');
            $date = \DateTime::createFromFormat('Y-m-d', $data);
            $dias = $this->esFinde($date);
            $date->add(new \DateInterval('P' . $dias . 'D'));
            while ($this->esFeriado($date) == 1) {
                $feriado = 1;
                $date->add(new \DateInterval('P' . $feriado . 'D'));

                $dias = $this->esFinde($date);
                $date->add(new \DateInterval('P' . $dias . 'D'));
            }
            $retiro->getDatosEnvios()->setFechaPactada($date);
        }

        $tracker = new Tracker();
        $tracker->setRetiro($retiro);
        $tracker->setEstado($repoestado);
        $tracker->setDistribuidor($distribuidor);
        $tracker->setReceptorFechaHora(new \DateTime($fecha));
        $tracker->setDetalles($obs);
        $tracker->setDni($dni);
        $tracker->setReceptorApellido($apenom);
        //$tracker->setReceptorNombre($apenom);
        $tracker->setNroPlanilla($retiro->getNroPlanilla());
        $tracker->setFechaPlanilla($retiro->getFechaPlanilla());
        $tracker->setUser($userWeb);
        $em->persist($firma);
        $em->persist($tracker);
        $em->persist($retiro);


        $em->flush();

        $view->setData(Array("status"=>200));
        return $view;
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

    private function esFinde($fecha){
        $dias = 0;
        if($fecha->format('N')>=6){
            $dias = 2;
        }
        if($fecha->format('N')==7){
            $dias = 1;
        }
        return $dias;
    }

    private function esFeriado($fecha){
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(f.id)');
        $qb->from('PresisGeneralBundle:Feriados','f');
        $qb->where('f.fecha = :fecha');
        $qb->setParameter('fecha', $fecha->format('Y-m-d'));

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }




}
