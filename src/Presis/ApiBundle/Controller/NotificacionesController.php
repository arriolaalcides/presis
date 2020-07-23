<?php

namespace Presis\ApiBundle\Controller;

use FOS\RestBundle\View\View;

use Presis\EstadoBundle\Entity\Estado;

use Doctrine\Common\Collections\Criteria;
use Presis\RetiroBundle\Entity\Comprador;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\RetiroBundle\Entity\Sender;
use Presis\GeneralBundle\Entity\Sucursal;
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


class NotificacionesController extends Controller
{
    private $SABADO = 6;

    /**
     * @RequestParam(name="retiro_id",nullable=false, description="Nro. de retiro")
     * @RequestParam(name="guia",nullable=true, description="Guias asociadas al retiro")
     * @RequestParam(name="codigo",nullable=true, description="Codigo de estado")
     * @RequestParam(name="descripcion",nullable=true, description="Descripcion del estado")
     * @RequestParam(name="fecha",nullable=true,description="Fecha de informe")
     * @RequestParam(name="movil_id",nullable=true,description="Movil que informo")
     * @RequestParam(name="obs",nullable=true,description="Movil que informo")
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

    public function postNotificacionAction(ParamFetcher $paramFetcher=null)
    {
        $retiro_id = $paramFetcher->get('retiro_id');
        $guia = $paramFetcher->get('guia');
        $codigo = $paramFetcher->get('codigo');
        $descripcion = $paramFetcher->get('descripcion');
        $fecha = $paramFetcher->get('fecha');
        $movil_id = $paramFetcher->get('movil_id');
        $obs = $paramFetcher->get('obs');

        $view = View::create();
        $view->setStatusCode(200);

        $em = $this->getDoctrine()->getManager();

        $constancia = $em->getRepository("ConstanciaRetiroBundle:ConstanciaRetiro")->findOneBy(array('id'=>$retiro_id));

        $repoestado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>$codigo));

        /*$delay = $repoestado->getDelayretiro();

        $newDate = $constancia->getFecha();
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
        $constancia->setFecha($date);*/

        $constancia->setEstado($codigo.' - '.$descripcion);
        $constancia->setFechaRetirado(new \DateTime($fecha));

        $distribuidor = $em->getRepository('DistribuidorBundle:Distribuidor')->findOneBy(array('id'=>$movil_id));

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('id'=>$guia));

        $userWeb = $entities = $em->getRepository('PresisUserBundle:User')->findOneBy(array("username"=>"web"));

        if($retiro){

            $retiro->setEstado($repoestado);
            $retiro->setFechaUltimoEstado(new \DateTime($fecha));
            $retiro->getDatosEnvios()->setFecha(new \DateTime($fecha));
            $retiro->setFechaHoraEntrega(new \DateTime($fecha));
            $retiro->setDetalleEntrega("INGRESO CEL");
            $retiro->setDistribuidor($distribuidor);
            $retiro->getDatosEnvios()->setDebeRetirarse(false);

            $this->calcularTiempoEnTransito($retiro);

            if($retiro->getDatosEnvios()->getConfirmada()!=true){
                $retiro->getDatosEnvios()->setConfirmada(true);
                $retiro->getDatosEnvios()->setFechaConfirmada(new \DateTime($fecha));
            }

            $tracker = new Tracker();
            $tracker->setRetiro($retiro);
            $tracker->setEstado($repoestado);
            $tracker->setDistribuidor($distribuidor);
            $tracker->setReceptorFechaHora(new \DateTime($fecha));
            $tracker->setDetalles("INGRESO CEL");
            $tracker->setUser($userWeb);

            $em->persist($tracker);
            $em->persist($retiro);
            $em->persist($constancia);
            $em->flush();
        }

        $em->persist($constancia);
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
