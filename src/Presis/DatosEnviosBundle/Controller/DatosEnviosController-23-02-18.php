<?php

namespace Presis\DatosEnviosBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Collections\ArrayCollection;
use Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiro;
use Presis\DatosEnviosBundle\Entity\DatosEnvios;
use Presis\GeneralBundle\Entity\Sucursal;
use Presis\GestionCelBundle\GestionCelBundle;
use Presis\TrackerBundle\Entity\Tracker;
use Proxies\__CG__\Presis\GeneralBundle\Entity\Cliente;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\RetiroBundle\Entity\Retiro;
use Presis\GestionCelBundle\Entity\GestionCel;
use Presis\RetiroBundle\Form\RetiroType2;
use Presis\RetiroBundle\Form\FindGuiaType;
use Presis\GestionCelBundle\Form\FinGestionCelType;
use Presis\GestionCelBundle\Form\SearchGestionCelType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Presis\GeneralBundle\Entity\ManifiestoCarga;

/**
 * DatosEnvios controller.
 *
 */
class DatosEnviosController extends Controller
{
    private $SABADO = 6;

    protected function getPrecioManager()
    {
        return $this->container->get('presis_precio');
    }

    private function getCliente($id_cliente) {
        return $this->getDoctrine()->getManager()
            ->getRepository("PresisGeneralBundle:Cliente")->find($id_cliente);
    }

    public function getFletePorPesoAction(Request $request) {
        $cliente = $this->getCliente($request->get('id_cliente'));
        if(!$cliente) return new Response(0);

        return new Response(
            $this->getPrecioManager()->calcularFletePorPeso(
                $request->get('peso'),
                $request->get('servicio'),
                $request->get('cpRemitente'),
                $request->get('cpDestinatario'),
                $cliente)
        );
    }

    public function getFletePorBultosAction(Request $request) {
        $cliente = $this->getCliente($request->get('id_cliente'));
        if(!$cliente) return 0;

        return new Response(
            $this->getPrecioManager()->calcularFletePorBultos(
                $request->get('bultos'),
                $request->get('servicio'),
                $request->get('cpRemitente'),
                $request->get('cpDestinatario'),
                $cliente)
        );
    }

    // 21-03 PICCINI - PARA CALCULAR EL PRECIO DE RANGO DE PESO EXCEDENTE
    public function getFleteRangoPesoAction(Request $request) {
        //die("CHAUUUUUUUUUUUUU");
        $cliente = $this->getCliente($request->get('id_cliente'));
        if(!$cliente) return 0;

        return new Response(
            //$this->getPrecioManager()->calcularFletePorBultos(
              $this->getPrecioManager()->calcularPesoPorRango(
                $request->get('kms'),
                $request->get('peso'),
                $request->get('servicio'),
                $request->get('cpRemitente'),
                $request->get('cpDestinatario'),
                $cliente)
        );
    }

    public function getFletePorValorDeclaradoAction(Request $request) {
        $cliente = $this->getCliente($request->get('id_cliente'));
        if(!$cliente) return 0;

        return new Response(
            $this->getPrecioManager()->calcularFletePorValorDeclarado($request->get('valor_declarado'), $cliente)
        );
    }

    public function formFindGuiaAction(Request $request)
    {
        $empresa = $this->container->getParameter('empresa');

        $securityContext = $this->container->get('security.context');

        $entity = new Retiro();

        $form = $this->createForm(new FindGuiaType($securityContext,$empresa), $entity, array(
            'action' => $this->generateUrl('datosenvios_show'),
            'method' => 'POST',
        ));

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $distribuidor = ($user->hasRole('ROLE_DISTRIBUIDOR'))? $user->getDistribuidor() : null;

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        return $this->render('DatosEnviosBundle:DatosEnvios:show.html.twig', array(
            'entity' => $entity,
            'distribuidor' => $distribuidor,
            'sucursal' => $sucursal,
            'form'   => $form->createView(),
        ));
    }


    public function pendientesInteriorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('DistribuidorBundle:Distribuidor')->findAll();

        $query = $em->createQuery('SELECT p.id, p.codigo, p.apellido, p.nombre FROM DistribuidorBundle:Distribuidor p 
        WHERE p.zona=:zona');
        $query->setParameter('zona', 'INTERIOR');
        $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

        return $this->render('DatosEnviosBundle:DatosEnvios:pendientes.html.twig', array(
            'entities' => $result,
        ));
    }

    public function pendientesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('DistribuidorBundle:Distribuidor')->findAll();

        $query = $em->createQuery('SELECT p.id, p.codigo, p.apellido, p.nombre FROM DistribuidorBundle:Distribuidor p 
        WHERE p.zona!=:zona');
        $query->setParameter('zona', 'INTERIOR');
        $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

        return $this->render('DatosEnviosBundle:DatosEnvios:pendientes.html.twig', array(
            'entities' => $result,
        ));
    }

    public function findTrackerAction(Request  $request)
    {
        $trackingNo = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();

        $retiros = $em->getRepository('TrackerBundle:Tracker')->findBy(
            array('retiro' => $trackingNo));

        if (!$retiros) {
            return new Response("No hay disponible un retiro con número de remito $trackingNo", Response::HTTP_CONFLICT);
        }

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($retiros, "json"));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    public function getPendientesMasLogisticaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository("PresisRetiroBundle:Retiro")->createQueryBuilder('r')
            ->join('DatosEnviosBundle:DatosEnvios', 'c')
            ->where('r.datosEnvios = c.id')
            ->andWhere('c.debeRetirarse = TRUE')
            ->andWhere('c.confirmada = TRUE')
            ->andWhere('r.estado=41');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if($user->hasRole('ROLE_CLIENTE')){
            $query->andWhere("r.cliente = :cliente")
                ->setParameter('cliente', $user->getCliente());
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    public function getPendientesAction(Request $request)
    {

        /*
         * public function getPendientesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository("PresisRetiroBundle:Retiro")->createQueryBuilder('r')
            ->join('DatosEnviosBundle:DatosEnvios', 'c')
            ->where('r.datosEnvios = c.id')
            ->andWhere('c.bultos > 0')
            ->andWhere('c.debeRetirarse = TRUE')
            ->andWhere('c.confirmada = TRUE');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if($user->hasRole('ROLE_CLIENTE')){
            $query->andWhere("r.cliente = :cliente")
                ->setParameter('cliente', $user->getCliente());
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();
        $query = $em->createQuery(
            'SELECT r.createguia, r.impreso, r.zonaOrigen, de.fecha, r.id, c.empresa, s.empresa AS senderEmpresa, CONCAT(s.calle, s.altura, s.piso, s.dpto) AS senderDireccion,
             s.localidad, s.provincia, s.cp, de.bultos, de.peso, e.nombre AS estadoDescripcion, cpc.subzona
             FROM 
             PresisRetiroBundle:Retiro r, 
             DatosEnviosBundle:DatosEnvios de, 
             PresisGeneralBundle:Cliente c,
             PresisRetiroBundle:Sender s,
             EstadoBundle:Estado e,
             PresisServicioBundle:CpCordon cpc
             WHERE
             r.datosEnvios = de AND
             r.cliente = c AND
             r.sender = s AND
             r.estado = e AND
             s.cp = cpc.cp AND
             cpc.subzona = :subzona AND
             de.debeRetirarse = :debeRetirarse AND 
             r.estado = 41 '
        );
        $query->setParameter('subzona','INT');
        $query->setParameter('debeRetirarse','1');

        /*$query->select('r,e')
            ->from('PresisRetiroBundle:Retiro','r')
            ->innerJoin('r.estado','e')
            ->innerJoin('r.datosEnvios','de')
            ->where('e.seleccionableParaRecorrido = TRUE')
            ->andWhere('de.confirmada = TRUE')
            ->andWhere('de.debeRetirarse = TRUE');*/

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if($user->hasRole('ROLE_CLIENTE')){
            $query->andWhere("r.cliente = :cliente")
                ->setParameter('cliente', $user->getCliente());
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    public function confirmarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DistribuidorBundle:Distribuidor')->findAll();

        return $this->render('DatosEnviosBundle:DatosEnvios:confirmar.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function getConfirmarPagosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /*$query = $em->getRepository("PresisRetiroBundle:Retiro")->createQueryBuilder('r')
            ->join('DatosEnviosBundle:DatosEnvios', 'c')
            ->where('r.datosEnvios = c.id')
            ->andWhere('c.cobrado = FALSE')
            ->andWhere('r.estado != 97');*/
        $query = $em->createQueryBuilder();

        $query->select('r,c,de')
            ->from('PresisRetiroBundle:Retiro','r')
            ->join('r.comprador','c')
            ->join('r.datosEnvios','de')
            ->where('r.comprador = c')
            ->andWhere('r.datosEnvios = de')
            ->andWhere('de.cobrado=FALSE')
            ->andWhere('r.estado != 97');

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    public function getPagosConfirmadosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $attrs = $request->request->all();

        $query = $em->createQueryBuilder();

        $query->select('r,c,de,cli')
            ->from('PresisRetiroBundle:Retiro','r')
            ->join('r.comprador','c')
            ->join('r.datosEnvios','de')
            ->join('r.cliente','cli')
            ->where('r.comprador = c')
            ->andWhere('r.datosEnvios = de')
            ->andWhere('de.cobrado=TRUE')
            ->andWhere('cli.cobroEfectivo=TRUE')
            ->andWhere('r.id LIKE :param OR 
            c.apenom LIKE :param OR 
            c.calle LIKE :param OR 
            c.localidad LIKE :param')
            ->orderBy('r.fechHora','DESC')
            ->setParameter('param', '%'.$attrs['search'].'%');

        $dontPaginate = true;
        if ($attrs["limite"] > 0) {
            $query->setFirstResult($attrs["pagina"] * $attrs["limite"])
                ->setMaxResults($attrs["limite"]);
            $dontPaginate = false;
        }
        unset($attrs["pagina"]);
        unset($attrs["limite"]);

        $paginator = new Paginator($query, $fetchJoinCollection = false);

        $result["total"] = count($paginator);
        $result["rows"] = $paginator->getQuery()->getResult();

        //$result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }


    public function getconfirmarretirosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository("PresisRetiroBundle:Retiro")->createQueryBuilder('r')
            ->join('DatosEnviosBundle:DatosEnvios', 'c')
            ->where('r.datosEnvios = c.id')
            ->andWhere('c.confirmada = FALSE');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if($user->hasRole('ROLE_CLIENTE')){

            if($user->isUserAdmin()==TRUE){
                $query->andWhere("r.cliente = :cliente")
                    ->setParameter('cliente', $user->getCliente());
            }else{
                $query->andWhere("r.createguia = :user")
                    ->setParameter('user', trim($user));
            }
        }

        if($user->hasRole('ROLE_DISTRIBUIDOR')){
            $query->andWhere("r.createguia = :user")
                ->setParameter('user', trim($user));
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }


    //BUSCAR GUIA WEB
    public function findGuiaAction(Request $request)

    {
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $attrs = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();
        $query->select('r,e')
            ->from('PresisRetiroBundle:Retiro','r')
            ->leftJoin('r.estado','e')
            ->leftJoin('r.datosEnvios','c')
            ->where('c.confirmada = TRUE');

        if($attrs["fechaDesde"]) {
            $attrs["fechaDesde"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaDesde"]);
            $query->andWhere('c.fecha >= :desde')
                ->setParameter('desde', $attrs["fechaDesde"]->format('Y-m-d 00:00:00'));
            unset($attrs["fechaDesde"]);
        }

        if($attrs["fechaHasta"]) {
            $attrs["fechaHasta"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaHasta"]);
            $query->andWhere('c.fecha <= :hasta')
                ->setParameter('hasta', $attrs["fechaHasta"]->format('Y-m-d 23:59:59'));
            unset($attrs["fechaHasta"]);
        }

        if($attrs["planillaDesde"]) {
            $attrs["planillaDesde"] = \DateTime::createFromFormat('d/m/Y', $attrs["planillaDesde"]);
            $query->andWhere('r.fechaPlanilla >= :planillaDesde')
                ->setParameter('planillaDesde', $attrs["planillaDesde"]->format('Y-m-d'));
            unset($attrs["planillaDesde"]);
        }

        if($attrs["planillaHasta"]) {
            $attrs["planillaHasta"] = \DateTime::createFromFormat('d/m/Y', $attrs["planillaHasta"]);
            $query->andWhere('r.fechaPlanilla <= :planillaHasta')
                ->setParameter('planillaHasta', $attrs["planillaHasta"]->format('Y-m-d'));
            unset($attrs["planillaHasta"]);
        }

        if($attrs["nroCta"]) {
            $query->andWhere("c.nroCta = :nroCta")
                ->setParameter('nroCta', $attrs["nroCta"]);
            unset($attrs["nroCta"]);
        }

        if($attrs["guiaAgente"]) {
            $query->andWhere("c.guiaAgente = :guiaAgente")
                ->setParameter('guiaAgente', $attrs["guiaAgente"]);
            unset($attrs["guiaAgente"]);
        }

        if($user->hasRole('ROLE_SUCURSAL')){
            $query->andWhere("r.sucursal = :sucursal")
                ->setParameter('sucursal', $user->getSucursal());
        }else{
            if((!$user->hasRole('ROLE_ADMIN') && !$user->hasRole('ROLE_VENDEDOR') && !$user->hasRole('ROLE_ADMINISTRACION')
                && !$user->hasRole('ROLE_DISTRIBUIDOR') && !$user->hasRole('ROLE_BACK_OFFICE'))){

                if($attrs["remitente"]) {
                    $query->andWhere("c.remitente = :remitente")
                        ->setParameter('remitente', $attrs["remitente"]);
                    unset($attrs["remitente"]);
                }

                if($attrs["destinatario"]) {
                    $query->andWhere("c.destinatario = :destinatario")
                        ->setParameter('destinatario', $attrs["destinatario"]);
                    unset($attrs["destinatario"]);
                }
            }
        }

        if($attrs["idDesde"]) {
            $query->andWhere('r.id >= :idDesde')
                ->setParameter('idDesde', $attrs["idDesde"]);
            unset($attrs["idDesde"]);
        }

        if($attrs["idHasta"]) {
            $query->andWhere('r.id <= :idHasta')
                ->setParameter('idHasta', $attrs["idHasta"]);
            unset($attrs["idHasta"]);
        }

        if($attrs["fechaUdesde"]) {
            $attrs["fechaUdesde"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaUdesde"]);
            $query->andWhere('r.fechaHoraEntrega >= :fechaUdesde')
                ->setParameter('fechaUdesde', $attrs["fechaUdesde"]->format('Y-m-d 00:00:00'));
            unset($attrs["fechaUdesde"]);
        }

        if($attrs["fechaUhasta"]) {
            $attrs["fechaUhasta"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaUhasta"]);
            $query->andWhere('r.fechaHoraEntrega <= :fechaUhasta')
                ->setParameter('fechaUhasta', $attrs["fechaUhasta"]->format('Y-m-d 23:59:59'));
            unset($attrs["fechaUhasta"]);
        }

        if($attrs["fechaUplanillaDesde"]) {
            $attrs["fechaUplanillaDesde"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaUplanillaDesde"]);
            $query->andWhere('r.fechaPlanilla >= :fechaUplanillaDesde')
                ->setParameter('fechaUplanillaDesde', $attrs["fechaUplanillaDesde"]->format('Y-m-d'));
            unset($attrs["fechaUplanillaDesde"]);
        }

        if($attrs["fechaUplanillaHasta"]) {
            $attrs["fechaUplanillaHasta"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaUplanillaHasta"]);
            $query->andWhere('r.fechaPlanilla <= :fechaUplanillaHasta')
                ->setParameter('fechaUplanillaHasta', $attrs["fechaUplanillaHasta"]->format('Y-m-d'));
            unset($attrs["fechaUplanillaHasta"]);
        }

        if($attrs["fechaFacturaDesde"]) {
            $attrs["fechaFacturaDesde"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaFacturaDesde"]);
            $query->andWhere('c.fechaFactura >= :fechaFacturaDesde')
                ->setParameter('fechaFacturaDesde', $attrs["fechaFacturaDesde"]->format('Y-m-d'));
            //die($attrs["fechaFacturaDesde"]->format('Y-m-d'));
            unset($attrs["fechaFacturaDesde"]);
        }

        if($attrs["fechaFacturaHasta"]) {
            $attrs["fechaFacturaHasta"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaFacturaHasta"]);
            $query->andWhere('c.fechaFactura <= :fechaFacturaHasta')
                ->setParameter('fechaFacturaHasta', $attrs["fechaFacturaHasta"]->format('Y-m-d'));
            unset($attrs["fechaFacturaHasta"]);
        }

        /*================ YA ANDAAAAAAAAAAAAAA ===========================*/
        if($attrs["conContrareembolso"]) {

            if ($attrs["conContrareembolso"] == "NO") {

                $attrs["conContrareembolso"] = 0;
                $query->andWhere("c.contrareembolso <= :valor2")
                    ->setParameter('valor2', $attrs["conContrareembolso"]);

            }else{

                $attrs["conContrareembolso"] = 1;
                $query->andWhere("c.contrareembolso > :valor")
                    ->setParameter('valor', $attrs["conContrareembolso"]);

            }
            unset($attrs["conContrareembolso"]);
        }
        /*===============================================================*/

        /*================ NO ANDAAAAAAAAAAAAAA ===========================*/

            if ($attrs["facturado"]==1) {
                $query->andWhere("c.facturado = true");
                unset($attrs["facturado"]);
            }else{
               $query->andWhere("c.facturado = false");
                unset($attrs["facturado"]);
            }

        /*===============================================================*/

        if($attrs["cordonOrigen"]) {
            $query->andWhere("c.cordonOrigen = :cordonOrigen")
                ->setParameter('cordonOrigen', $attrs["cordonOrigen"]);
            unset($attrs["cordonOrigen"]);
        }

        if($attrs["cordonDestino"]) {
            $query->andWhere("c.cordonDestino = :cordonDestino")
                ->setParameter('cordonDestino', $attrs["cordonDestino"]);
            unset($attrs["cordonDestino"]);
        }

        if ($attrs["sinFactura"] == "true") {
            $query->andWhere("c.nroFactura IS NULL");
        }
        unset($attrs["sinFactura"]);

        //$conditions = array('r.estado = 41', 'r.estado = 40', 'r.estado = 42');
        if($attrs["estado"]) {
            $estados = $attrs["estado"];
            $x = array();
            foreach ($estados as $valor) {
                array_push($x, 'r.estado ='.$valor);
            }

            $orX = $query->expr()->orX();
            $orX->addMultiple($x);

            $query->andWhere($orX);
            unset($attrs["estado"]);
        }

        $dontPaginate = true;
        if ($attrs["limite"] > 0) {
            $query->setFirstResult($attrs["pagina"] * $attrs["limite"])
                ->setMaxResults($attrs["limite"]);
            $dontPaginate = false;
        }
        unset($attrs["pagina"]);
        unset($attrs["limite"]);

        /*if($user->hasRole('ROLE_ADMIN')){
            if ($attrs["sinEstado"] == "true") {
                $query->andWhere("r.estado IS NULL");
            }else{
                $query->andWhere("r.estado != 106");
            }
        }else{
            if ($attrs["sinEstado"] == "true") {
                $query->andWhere("r.estado IS NULL");
            }else{
                $query->andWhere("e.seleccionableParaWeb != FALSE");
            }
        }*/

        if($user->hasRole('ROLE_CLIENTE')){
            if ($attrs["sinEstado"] == "true") {
                $query->andWhere("r.estado IS NULL");
            }else{
                $query->andWhere("e.seleccionableParaWeb != FALSE");
            }
        }else{
            if ($attrs["sinEstado"] == "true") {
                $query->andWhere("r.estado IS NULL");
            }else{
                $query->andWhere("r.estado != 106");
            }
        }

        unset($attrs["sinEstado"]);

        foreach ($attrs as $key => $value) {
            if (trim($value) !== '') {
                $query->andWhere("r.$key = :$key")
                    ->setParameter("$key", $value);
            }
        }

        if($user->hasRole('ROLE_CLIENTE')){

            if($user->isUserAdmin()==TRUE){
                $query->andWhere("r.cliente = :cliente")
                    ->setParameter('cliente', $user->getCliente());
            }else{
                $query->andWhere("r.createguia = :user")
                    ->setParameter('user', trim($user));
            }
        }

        //01-01-2017 - PICCINI - SACO ESTADO PEDIDO DE ECOMMERCE Y SOLICITUD CONFIRMADA PEDIDO POR LEO/JAVI
        //$query->andWhere("r.estado != 41");
        //22-01-2017 - PICCINI - SACO ESTADO PEDIDO DE ECOMMERCE Y SOLICITUD CONFIRMADA PEDIDO POR LEO/JAVI
        //$query->andWhere("r.estado != 106");

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result["total"] = count($paginator);
        $result["rows"] = $paginator->getQuery()->getResult();

        if ($dontPaginate) {
            return $this->downloadAsExcel($result["rows"]);
        } else {
            $serializer = $this->get('jms_serializer');
            $json = $serializer->serialize($result, "json");

            return new Response($json);
        }
    }

    /**
     * Agrega al retiro indicado los dos datos de su factura
     *
     *                   id_retiro;nro_factura;fecha_factura
     *
     */
    public function agregarDatosFacturaAction(Request $request, $id_retiro, $nro_factura, $fecha_factura)
    {
        $em = $this->getDoctrine()->getManager();

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);

        if (!$retiro) {
            return new Response("El retiro $id_retiro no existe", Response::HTTP_CONFLICT);
        }

        if (!$nro_factura) {
            return new Response("No se ingresó nro de factura para el retiro $id_retiro", Response::HTTP_CONFLICT);
        }

        $nro_factura = urldecode($nro_factura);
        $retiro->getDatosEnvios()->setNroFactura($nro_factura);

        if($fecha_factura) {
            $fecha_factura = urldecode($fecha_factura);
            $retiro->getDatosEnvios()->setFechaFactura(\DateTime::createFromFormat('m/d/Y', $fecha_factura));
        }

        $em->persist($retiro);
        $em->flush();

        return new Response("Factura del retiro $id_retiro: $nro_factura, $fecha_factura");
    }

    /**
     * Creates a new Retiro entity.
     *
     */
    public function createAction(Request $request)
    {

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $entity = new Retiro();

        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $securityContext = $this->container->get('security.context');
            $user=$securityContext->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();

            if($form->get("saveRemitente")->getData()=='SI'){
                $searchRemitente = $em->getRepository('RemitentesBundle:Remitente')->findOneBy(array("codigo"=>$entity->getSender()->getCodigo()));
                if($searchRemitente){
                    $em->remove($searchRemitente);
                    $em->flush();
                }
                $remitente = new \Presis\RemitentesBundle\Entity\Remitente();
                $remitente->setCodigo($entity->getSender()->getCodigo());
                $remitente->setEmpresa($entity->getSender()->getEmpresa());
                $remitente->setRemitente($entity->getSender()->getRemitente());
                $remitente->setCalle($entity->getSender()->getCalle());
                $remitente->setAltura($entity->getSender()->getAltura());
                $remitente->setPiso($entity->getSender()->getPiso());
                $remitente->setDpto($entity->getSender()->getDpto());
                $remitente->setLocalidad($entity->getSender()->getLocalidad());
                $remitente->setProvincia($entity->getSender()->getProvincia());
                $remitente->setCp($entity->getSender()->getCp());
                $remitente->setCelular($entity->getSender()->getCelular());
                $remitente->setOtherInfo($entity->getSender()->getOtherInfo());
                $remitente->setCliente($entity->getDatosEnvios()->getCliente());
                $remitente->setMail($entity->getSender()->getEmail());
                $remitente->setCelular($entity->getSender()->getCelular());
                $remitente->setUser($user);
                $em2 = $this->getDoctrine()->getManager();
                $em2->persist($remitente);
                $em2->flush();
                //die("ACA GUARDA EL REMITENTE");
                //die("HOLA: ".$entity->getSender()->getEmpresa());
            }
            if($form->get("saveDestinatario")->getData()=='SI'){
                $searchDestinatario = $em->getRepository('DestinatariosBundle:Destinatarios')->findOneBy(array("codigo"=>$entity->getComprador()->getCodigo()));
                if($searchDestinatario){
                    $em->remove($searchDestinatario);
                    $em->flush();
                }
                $destinatario = new \Presis\DestinatariosBundle\Entity\Destinatarios();
                $destinatario->setCodigo($entity->getComprador()->getCodigo());
                $destinatario->setEmpresa($entity->getComprador()->getEmpresa());
                $destinatario->setApellidoNombre($entity->getComprador()->getApenom());
                $destinatario->setCalle($entity->getComprador()->getCalle());
                $destinatario->setAltura($entity->getComprador()->getAltura());
                $destinatario->setPiso($entity->getComprador()->getPiso());
                $destinatario->setDpto($entity->getComprador()->getDpto());
                $destinatario->setLocalidad($entity->getComprador()->getLocalidad());
                $destinatario->setProvincia($entity->getComprador()->getProvincia());
                $destinatario->setCp($entity->getComprador()->getCp());
                $destinatario->setOtherInfo($entity->getComprador()->getOtherInfo());
                $destinatario->setCelular($entity->getComprador()->getCelular());
                $destinatario->setMail($entity->getComprador()->getEmail());
                $destinatario->setCliente($user->getCliente());
                $destinatario->setUser($user);
                $em3 = $this->getDoctrine()->getManager();
                $em3->persist($destinatario);
                $em3->flush();
            }

            $securityContext = $this->container->get('security.context');
            $user=$securityContext->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();

            /*$cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                array('cp' => $entity->getComprador()->getCp()));*/

            // $entity->setCordonEntrega($cpCordonEntrega->getCordon());

            $cobraEfectivo = $em->getRepository("PresisGeneralBundle:Cliente")->findOneBy(array("id"=>$entity->getDatosEnvios()->getCliente()->getId()));

            if($cobraEfectivo->getCobroEfectivo()){
                $entity->getDatosEnvios()->setCobrado(false);
            }else{
                $entity->getDatosEnvios()->setCobrado(true);
            }

            /*$cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                array('cp' => $entity->getComprador()->getCp()));


            $cpCordonOrigen = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                array('cp' => $entity->getSender()->getCp()));

            $entity->setZonaOrigen($cpCordonOrigen->getZona());

            $entity->setSubZonaOrigen($cpCordonOrigen->getSubzona());
            $entity->setSubZonaDestino($cpCordonEntrega->getSubzona());

            $entity->setZona($cpCordonEntrega->getZona());


            $entity->getDatosEnvios()->setCordonOrigen($cpCordonOrigen->getCordon());
            $entity->getDatosEnvios()->setCordonDestino($cpCordonEntrega->getCordon());*/

            //PICCINI - AGREGADO NO GUARDABA FECHA_HORA EN RETIRO AL GENERAR GUIA MANUAL
            $entity->setFechHora(new \DateTime('now'));
            $entity->setCliente($entity->getDatosEnvios()->getCliente());
            //$this->calcularTiempoEnTransito($entity);
            $entity->setCreateguia($user);
            $em->persist($entity);
            $em->flush();

            if($form["datosenvios"]["tipoOp"]->getData()=='1'){
                $solicitudRetiro = new ConstanciaRetiro();
                $solicitudRetiro->setRetiro($entity->getId());
                $solicitudRetiro->setCalle($entity->getSender()->getCalle());
                $solicitudRetiro->setAltura($entity->getSender()->getAltura());
                $solicitudRetiro->setPiso($entity->getSender()->getPiso());
                $solicitudRetiro->setDpto($entity->getSender()->getDpto());
                $solicitudRetiro->setLocalidad($entity->getSender()->getLocalidad());
                $solicitudRetiro->setProvincia($entity->getSender()->getProvincia());
                $solicitudRetiro->setCp($entity->getSender()->getCp());
                $solicitudRetiro->setUsuario($user);
                $solicitudRetiro->setCliente($entity->getDatosEnvios()->getCliente());
                $solicitudRetiro->setObservaciones($entity->getSender()->getOtherInfo());
                $solicitudRetiro->setContacto($entity->getSender()->getRemitente());
                $solicitudRetiro->setTelefono($entity->getSender()->getCelular());
                $solicitudRetiro->setBultos($entity->getDatosEnvios()->getBultos());
                $solicitudRetiro->setPeso($entity->getDatosEnvios()->getVolumen());
                $solicitudRetiro->setTimestamp(new \DateTime('now'));
                $solicitudRetiro->setFranja('TODO EL DIA');
                $em->persist($solicitudRetiro);
                $em->flush();

            }
            /* También actualiza la entidad Tracker */
            /*$tracker = new Tracker();
            $tracker->setRetiro($entity);
            $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
            $em->persist($tracker);
            $em->flush();*/

            //08-12-17 AGREGO PARA QUE PONGA EL ESTADO SI EL CLIENTE ES MOVISTAR, ASI LO TRAE A LA GENERACION DE BOLSINES
            if(!$user->hasRole('ROLE_ADMIN')){
                if($user->getCliente()->getEmpresa()=='MOVISTAR'){
                    $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>'NRCEC'));
                    $entity->getGestionCel()->setEstado($estado);
                    $entity->getGestioncel()->setSucursal($user->getSucursal()->getCodSuc());
                    $entity->getGestioncel()->setTrayecto('aGalander');
                    $entity->getDatosEnvios()->setBultos(1);
                    $em->persist($entity);
                    $em->flush();
                }
            }

            if($user->hasRole('ROLE_ADMIN')){
                return $this->redirect($this->generateUrl('datosenvios_edit', array('id' => $entity->getId())));
            }else{
                return $this->redirect($this->generateUrl('datosenvios_edit_client', array('id' => $entity->getId())));
            }

        }
        $var = $this->container->getParameter('empresa');
        if($var=='maslogistica'){
            if($user->hasRole('ROLE_ADMIN')){
                return $this->render('DatosEnviosBundle:DatosEnvios:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            }else{
                if($user->getCliente()->getEmpresa()=='MOVISTAR'){
                    return $this->render('DatosEnviosBundle:DatosEnvios:new-movistar.html.twig', array(
                        'entity' => $entity,
                        'form'   => $form->createView(),
                    ));
                }else{
                    return $this->render('DatosEnviosBundle:DatosEnvios:new.html.twig', array(
                        'entity' => $entity,
                        'form'   => $form->createView(),
                    ));
                }
            }
        }else{
            if($user->hasRole('ROLE_ADMIN')){
                return $this->render('DatosEnviosBundle:DatosEnvios:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            }else{
                return $this->render('DatosEnviosBundle:DatosEnvios:new-client.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            }
        }
    }

    public function createFromCSVAction(Request $request)
    {
        $fechin = null;

        //09-01 PICCINI VARIABLE GLOBAL EMPRESA
        $var = $this->container->getParameter('empresa');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $post = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        $estado = $em->getRepository('EstadoBundle:Estado')->find(41);

        //$hydrator = new DoctrineHydrator($em);

        $hydrator = new MyCustomHydrator($em);

        $r = new Retiro();

        $post = $this->prepareEntities($post);

        $retiro = $hydrator->hydrate($post, $r);

        try {
            if(!$user->hasRole('ROLE_ADMIN') and !$user->hasRole('ROLE_SUPERVISOR_OPERATIVO') and !$user->hasRole('ROLE_OPERATIVO') and !$user->hasRole('ROLE_BACK_OFFICE')){
               die("ENTRO ACA");
                $cliente = $securityContext->getToken()->getUser()->getCliente();
                $r->setCliente($cliente);
                $r->setFechHora(new \DateTime());
                //18-04-17 PICCINI, LO PONGO EN FALSE A PEDIDO DE JAVIER ORLANDO.
                $r->getDatosEnvios()->setConfirmada(false);
                $r->getDatosEnvios()->setFechaConfirmada(new \DateTime());
                $r->setCreateguia($user);
                //FECHIN PARA EL TRACKER
                $fechin =  new \DateTime();
                //11-01-2017 PICCINI - VALIDA SI EXISTE EL REMITO DEL CLIENTE
                $remitoCliente = $em->getRepository("PresisRetiroBundle:Retiro")->findOneBy(
                    array(
                        'remito' => $retiro->getRemito(),
                        'cliente' => $cliente,
                    ));
                if($remitoCliente){
                    throw new Exception('El remito ya existe');
                }
            }
            $retiro->getDatosEnvios()->setCliente($retiro->getCliente());

            $r->setFechHora($r->getDatosEnvios()->getFecha());
            $fechin = $r->getFechHora();
            /*if($retiro->getDatosEnvios()->getBultos()<"0"){
                die("FALTA CAMPO BULTOS");
            }*/

            $this->calcularCostos($retiro);

            if($var=='caktus'){
                $retiro->setFechHora(new \DateTime());
                $retiro->getDatosEnvios()->setConfirmada(1);
            }
            if($var=='fasttrack'){
                $retiro->setEstado($estado);
                $retiro->setFechaHoraEntrega($fechin);
            }

            if($var!='caktus'){
                $this->calcularTiempoEnTransito($retiro);

                $cpCordonOrigen = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                    array('cp' => $retiro->getSender()->getCp()));

                $cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                    array('cp' => $retiro->getComprador()->getCp()));

               // $cpCordon = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(array('cp' => $cp));

                if(!$cpCordonOrigen){
                    throw new Exception('Ocurrio un problema con el cordon de origen');
                }

                if($retiro->getDatosEnvios()->getConfirmada()==true){
                    $r->getDatosEnvios()->setFechaConfirmada($r->getDatosEnvios()->getFecha());
                }

                $cordonOrigen = $cpCordonOrigen->getCordon()->getDescripcion();
                $cordonDestino = $cpCordonEntrega->getCordon()->getDescripcion();

                $retiro->setSubZonaOrigen($cpCordonOrigen->getSubzona());
                $retiro->setSubZonaDestino($cpCordonEntrega->getSubzona());

                $retiro->setServicio($retiro->getDatosEnvios()->getServicio());
                $retiro->getDatosEnvios()->setCordonOrigen($cordonOrigen);
                $retiro->getDatosEnvios()->setCordonDestino($cordonDestino);
                $retiro->setZonaOrigen($cpCordonOrigen->getZona());
                $retiro->setZona($cpCordonEntrega->getZona());
            }

            if($retiro->getDatosEnvios()->getConfirmada()==true){
                /* PICCINI - 23-03 PEDIDO POR JAVIER*/
                $tracker = new Tracker();
                $tracker->setRetiro($retiro);
                $tracker->setEstado($estado);
                $tracker->setReceptorFechaHora($fechin);
                $tracker->setDetalles("IMPORTADA");
                $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
                $em->persist($tracker);
                /*=================================*/
            }

            $retiro->setCreateGuia($user);
            $em->persist($retiro);
            $em->flush();

            return new Response("Número " . $retiro->getId() . ", remito " . $retiro->getRemito());
        } catch (Exception $e) {
            return new Response("Remito: ".$retiro->getRemito()."-" .$e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function saveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $entity = new Retiro();

        $form = $this->createForm(new RetiroConfirmType(), $entity, array(
            'action' => $this->generateUrl('retiro_save'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();
        $sucu=$entity->getSucursal();
        $sender=$this->setSenderData($sucu);
        $entity->setSender($sender);


        $entity->setCliente($user->getCliente());
        $entity->setFechHora(new \DateTime('now'));
        $repoCordon = $this->getDoctrine()->getRepository('PresisServicioBundle:CpCordon');
        //$cordon=$repoCordon->findCordonInCp($cp);
        $cpcordon=$repoCordon->findOneByCp($entity->getComprador()->getCp());


        $entity->setCordonEntrega($cpcordon->getCordon());
        $entity->setPrestador($this->getPrecioManager()->generatePrestador($cpcordon));
        $this->calcularTiempoEnTransito($entity);

        $em->persist($entity);
        $em->flush();
        return $this->render('PresisRetiroBundle:Retiro:index.html.twig', array(
            'nada' => "hola",
        ));

    }
    public function confirmAction($retiro){
        $form = $this->createForm(new RetiroConfirmType(), $retiro, array(
            'action' => $this->generateUrl('retiro_save'),
            'method' => 'POST',
            'read_only' => true,
        ));

        $form->add('submit', 'submit', array('label' => 'Confirmar Retiro','attr' => array('class'=> 'btn btn-success')));

        return $this->render('PresisRetiroBundle:Retiro:confirm.html.twig', array(
            'entity' => $retiro,
            'form' =>$form->createView(),

        ));

    }
    public function ajaxHabilitadosAction(Request $request){
        $cp=$request->get("cp");
        $suc=$request->get("sucursal");

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em=$this->getDoctrine()->getManager();
        $q=$em->getRepository("PresisServicioBundle:Servicio")->findHabilitados($user,$cp,$suc);
        $servicios=$q->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $data=$serializer->serialize($servicios, 'json');
        return new Response($data);

    }
    /**
     * Creates a form to create a Retiro entity.
     *
     * @param Retiro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Retiro $entity)
    {
        $empresa = $this->container->getParameter('empresa');

        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new RetiroType2($securityContext,"insertar",$empresa),$entity, array(
            'action' => $this->generateUrl('datosenvios_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'GENERAR GUIA','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /*
     * Para Mas Logitica
     */
    public function pendientesRetiroAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        $entities = $em->getRepository('DistribuidorBundle:Distribuidor')->findBy(array("sucursal"=>$user->getSucursal()));

        return $this->render('DatosEnviosBundle:DatosEnvios:pendientes-retiro.html.twig', array(
            'entities' => $entities,
        ));
    }

    /*
     * Para Mas Logitica
     */
    public function pendientesPagoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('DistribuidorBundle:Distribuidor')->findAll();

        return $this->render('DatosEnviosBundle:DatosEnvios:pendientes-pago.html.twig');
    }

    /*
     * Para Mas Logitica
     */
    public function pagosConfirmadosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('DistribuidorBundle:Distribuidor')->findAll();

        return $this->render('DatosEnviosBundle:DatosEnvios:pagos-confirmados.html.twig');
    }


    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showPendientesAction(){
        $format = $this->get('request')->get('_format');

        $em = $this->getDoctrine()->getManager();
        $repo=$em->getRepository("PresisRetiroBundle:Retiro");
        $user=$this->get('security.context')->getToken()->getUser();
        $cont=0;
        $arrtotal=array();

        $vouchers=$repo->findBy(array('impreso' => 'false','cliente'=>$user->getCliente()));
        foreach($vouchers as $voucher){
            $cont++;
            if ($cont==1){
                $arrpare=array();
            }

            ///   $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);
            //$entity=$repo->findById($voucher->getId());
            //$voucher->setImpreso(true);
            $em->persist($voucher);
            array_push($arrpare,$voucher);
            if ($cont==2){
                array_push($arrtotal,$arrpare);

                $cont=0;
            }        }
        if ($cont==1){
            array_push($arrtotal,$arrpare);

        }
        $em->flush();


        //  $format="pdf";
        return $this->render(sprintf('PresisRetiroBundle:Default:dupl.%s.twig', $format), array(
            'vouchers' => $arrtotal,
            'attr'=>array('target'=>'_blank'),
        ));
    }
    /**
     * Displays a form to create a new Retiro entity.
     *
     */

    public function newAction()
    {
        $empresa = $this->container->getParameter('empresa');
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $entity = new Retiro();
        $form   = $this->createCreateForm($entity);

        if($empresa=='maslogistica'){
            if($user->hasRole('ROLE_ADMIN')){
                return $this->render('DatosEnviosBundle:DatosEnvios:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            }else{
                if($user->getCliente()->getEmpresa()=='MOVISTAR'){
                    return $this->render('DatosEnviosBundle:DatosEnvios:new-movistar.html.twig', array(
                        'entity' => $entity,
                        'form'   => $form->createView(),
                    ));
                }else{
                    return $this->render('DatosEnviosBundle:DatosEnvios:new.html.twig', array(
                        'entity' => $entity,
                        'form'   => $form->createView(),
                    ));
                }
            }
        }
        if($empresa!='maslogistica'){
            if($user->hasRole('ROLE_ADMIN')){
                return $this->render('DatosEnviosBundle:DatosEnvios:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            }else{
                return $this->render('DatosEnviosBundle:DatosEnvios:new-client.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            }
        }
    }
    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showEtiAction(){

        $request=$this->container->get('request_stack')->getCurrentRequest();
        $ids=$request->get('ids');
        $cont=0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal=array();

        $arra=new ArrayCollection();
        $arrpa2=array();

        foreach($ids as $id) {

            $cont++;
            if ($cont==1){
                $arrpare=array();
            }

            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            $arra->add($entity);
            $entity->setImpreso(true);
            $em->persist($entity);
            array_push($arrpare,$entity);
            array_push($arrpa2,$entity);

            if ($cont==2){
                array_push($arrtotal,$arrpare);

                $cont=0;
            }
        }
        if ($cont==1){
            array_push($arrtotal,$arrpare);
        }


        $em->flush();


        $format = $this->get('request')->get('_format');
        //  $format="pdf";
        return $this->render(sprintf('PresisRetiroBundle:Default:eti.%s.twig', $format), array(
            'vouchers' => $arrpa2,
            'attr'=>array('target'=>'_blank'),
        ));


    }
    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showVoucherAction(){

        $request=$this->container->get('request_stack')->getCurrentRequest();
        $ids=$request->get('ids');
        $cont=0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal=array();

        $arra=new ArrayCollection();

        foreach($ids as $id) {

            $cont++;
            if ($cont==1){
                $arrpare=array();
            }

            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            $arra->add($entity);
            $entity->setImpreso(true);
            $em->persist($entity);
            array_push($arrpare,$entity);
            if ($cont==2){
                array_push($arrtotal,$arrpare);

                $cont=0;
            }
        }
        if ($cont==1){
            array_push($arrtotal,$arrpare);
        }


        $em->flush();


        $format = $this->get('request')->get('_format');
        //  $format="pdf";
        return $this->render(sprintf('PresisRetiroBundle:Default:dupl.%s.twig', $format), array(
            'vouchers' => $arrtotal,
            'attr'=>array('target'=>'_blank'),
        ));


    }

    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entity = $em->getRepository('DatosEnviosBundle:DatosEnvios')->find($id);
        return $this->render('DatosEnviosBundle:DatosEnvios:show.html.twig');
    }

    public function calcularPesoAction(Request $request) {

        $empresa = $this->container->getParameter('empresa');

        $vars = $request->request->all();

        $em = $this->getDoctrine()->getManager();
        $cliente = $em->getRepository('PresisGeneralBundle:Cliente')->find($vars['id_cliente']);

        $peso = $this->calcularPeso($vars, $cliente, $empresa);

        return new Response($peso);
    }

    /**
     * Para un cliente y dos cp, determina el servicio que corresponde
     *
     * @param Request $request
     * @return Response
     */
    public function getServicioAdecuadoAction(Request $request) {
        $post = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        $cliente = $em->getRepository('PresisGeneralBundle:Cliente')->find($post['id_cliente']);

        $cpCordonRetiro = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $post['sender_cp'])
        );
        $cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $post['comprador_cp'])
        );

        if(!$cpCordonRetiro || !$cpCordonEntrega || !$cliente) {
            return new Response('Parámetros incompletos', Response::HTTP_NOT_FOUND);
        }

        //die("Entrega: ".$cpCordonEntrega->getCordon()."--"." Retiro: ".$cpCordonRetiro->getCordon());

        $precio = $em->getRepository("PresisServicioBundle:Precio")->findOneBy(
            array(
                'lista' => $cliente->getLista(),
                'cordonEntrega' => $cpCordonEntrega->getCordon(),
                'cordonRetiro' => $cpCordonRetiro->getCordon(),
            )
        );

        if (!$precio) {
            return new Response('No hay servicio para la ruta', Response::HTTP_NOT_FOUND);
        }

        return new Response($precio->getServicio()->getId());
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function getSeguroAction(Request $request) {
        $post = $request->request->all();
        $cliente = $this->getCliente($post['id_cliente']);

        if(!$cliente) {
            return 0;
        }

        return new Response(
            $this->getPrecioManager()->calcularSeguro($post['valor_declarado'], $cliente)
        );
    }


    /**
     * Displays a form to edit an existing DatosEnvios entity.
     *
     */
    public function editAction($id)
    {

        $empresa = $this->container->getParameter('empresa');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('id'=>$id,'cliente'=>$user->getCliente()));

        if (!$entity) {
            return New Response("GUIA NO ENCONTRADA");
        }

        $editForm = $this->createEditForm($entity);

        $deleteForm = $this->createDeleteForm($id);

        if($empresa=='maslogistica'){
            if($user->hasRole('ROLE_ADMIN')){
                return $this->render('DatosEnviosBundle:DatosEnvios:edit.html.twig', array(
                    'entity'      => $entity,
                    'edit_form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }else{
                if($user->getCliente()->getEmpresa()=='MOVISTAR'){
                    return $this->render('DatosEnviosBundle:DatosEnvios:edit-movistar.html.twig', array(
                        'entity'      => $entity,
                        'edit_form'   => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                    ));
                }else{
                    return $this->render('DatosEnviosBundle:DatosEnvios:edit-movistar.html.twig', array(
                        'entity'      => $entity,
                        'edit_form'   => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                    ));
                }
            }
        }
        if(($empresa=='caktus') || ($empresa=='fasttrack')){
            if($user->hasRole('ROLE_ADMIN')){
                return $this->render('DatosEnviosBundle:DatosEnvios:edit.html.twig', array(
                    'entity'      => $entity,
                    'edit_form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }else{
                return $this->render('DatosEnviosBundle:DatosEnvios:edit-client.html.twig', array(
                    'entity'      => $entity,
                    'edit_form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }
    }

    /**update
     * Creates a form to edit a Retiro entity.
     *
     * @param Retiro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Retiro $entity)
    {
        $edit = 0;
        $empresa = $this->container->getParameter('empresa');
        $securityContext = $this->container->get('security.context');
        $em = $this->getDoctrine()->getEntityManager();
        $form = $this->createForm(new RetiroType2($securityContext,"editar",$empresa), $entity, array(
            'action' => $this->generateUrl('datosenvios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            //'read_only' => true,
        ));

        $form->add('submit', 'submit', array('label' => 'GUARDAR CAMBIOS', 'attr' => array(
            'class' => 'btn btn-danger btn-sm custom-btn'
        )));

        return $form;
    }

    public function setSenderData($sucursal){
        $sender=new Sender();

        $sender->setAltura($sucursal->getAltura());
        $sender->setCp($sucursal->getCp());
        $sender->setCalle($sucursal->getCalle());
        $sender->setDpto($sucursal->getDpto());
        $sender->setEmpresa($sucursal->getCliente()->getEmpresa());
        $sender->setOtherInfo($sucursal->getOtherInfo());
        return $sender;
    }

    /**
     * Edits an existing Retiro entity.
     *
     */

    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retiro entity.');
        }

        $request->request->set('presis_retirobundle_retiro_sender_cp', '');

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if($editForm->get("saveRemitente")->getData()=='SI'){
                $searchRemitente = $em->getRepository('RemitentesBundle:Remitente')->findOneBy(array("codigo"=>$entity->getSender()->getCodigo()));
                if($searchRemitente){
                    $em->remove($searchRemitente);
                    $em->flush();
                }
                $remitente = new \Presis\RemitentesBundle\Entity\Remitente();
                $remitente->setCodigo($entity->getSender()->getCodigo());
                $remitente->setEmpresa($entity->getSender()->getEmpresa());
                $remitente->setRemitente($entity->getSender()->getRemitente());
                $remitente->setCalle($entity->getSender()->getCalle());
                $remitente->setAltura($entity->getSender()->getAltura());
                $remitente->setPiso($entity->getSender()->getPiso());
                $remitente->setDpto($entity->getSender()->getDpto());
                $remitente->setLocalidad($entity->getSender()->getLocalidad());
                $remitente->setProvincia($entity->getSender()->getProvincia());
                $remitente->setCp($entity->getSender()->getCp());
                $remitente->setCelular($entity->getSender()->getCelular());
                $remitente->setOtherInfo($entity->getSender()->getOtherInfo());
                $remitente->setCliente($entity->getDatosEnvios()->getCliente());
                $remitente->setMail($entity->getSender()->getEmail());
                $remitente->setCelular($entity->getSender()->getCelular());
                $remitente->setUser($user);
                $em2 = $this->getDoctrine()->getManager();
                $em2->persist($remitente);
                $em2->flush();
            }

            if($editForm->get("saveDestinatario")->getData()=='SI'){
                $searchDestinatario = $em->getRepository('DestinatariosBundle:Destinatarios')->findOneBy(array("codigo"=>$entity->getComprador()->getCodigo()));
                if($searchDestinatario){
                    $em->remove($searchDestinatario);
                    $em->flush();
                }
                $destinatario = new \Presis\DestinatariosBundle\Entity\Destinatarios();
                $destinatario->setCodigo($entity->getComprador()->getCodigo());
                $destinatario->setEmpresa($entity->getComprador()->getEmpresa());
                $destinatario->setApellidoNombre($entity->getComprador()->getApenom());
                $destinatario->setCalle($entity->getComprador()->getCalle());
                $destinatario->setAltura($entity->getComprador()->getAltura());
                $destinatario->setPiso($entity->getComprador()->getPiso());
                $destinatario->setDpto($entity->getComprador()->getDpto());
                $destinatario->setLocalidad($entity->getComprador()->getLocalidad());
                $destinatario->setProvincia($entity->getComprador()->getProvincia());
                $destinatario->setCp($entity->getComprador()->getCp());
                $destinatario->setOtherInfo($entity->getComprador()->getOtherInfo());
                $destinatario->setCelular($entity->getComprador()->getCelular());
                $destinatario->setMail($entity->getComprador()->getEmail());
                $destinatario->setCliente($user->getCliente());
                $destinatario->setUser($user);
                $em3 = $this->getDoctrine()->getManager();
                $em3->persist($destinatario);
                $em3->flush();
            }

            //$this->calcularTiempoEnTransito($entity);

            //11-01-2017 PICCINI VALIDAR ZONA / ZONA ORIGEN / CORDON ORIGEN / CORDON DESTINO
            /*$cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                array('cp' => $entity->getComprador()->getCp()));
            $cpCordonOrigen = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                array('cp' => $entity->getSender()->getCp()));
            $entity->setZonaOrigen($cpCordonOrigen->getZona());
            $entity->setZona($cpCordonEntrega->getZona());
            $entity->setCliente($entity->getDatosEnvios()->getCliente());
            $entity->getDatosEnvios()->setCordonOrigen($cpCordonOrigen->getCordon());
            $entity->getDatosEnvios()->setCordonDestino($cpCordonEntrega->getCordon());

            $entity->setSubZonaOrigen($cpCordonOrigen->getSubzona());
            $entity->setSubZonaDestino($cpCordonEntrega->getSubzona());*/

            //$this->calcularTiempoEnTransito($entity);
            $em->flush();

            //08-12-17 AGREGO PARA QUE PONGA EL ESTADO SI EL CLIENTE ES MOVISTAR, ASI LO TRAE A LA GENERACION DE BOLSINES
            if(!$user->hasRole('ROLE_ADMIN')){
                if($user->getCliente()->getEmpresa()=='MOVISTAR'){
                    $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>'NRCEC'));
                    $entity->getGestionCel()->setEstado($estado);
                    $entity->getGestioncel()->setSucursal($user->getSucursal()->getCodSuc());
                    $entity->getGestioncel()->setTrayecto('aGalander');
                    $em->persist($entity);
                    $em->flush();
                }
            }

            return $this->redirect($this->generateUrl('datosenvios_edit', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('datosenvios_edit', array('id' => $id)));
    }
    /**
     * Deletes a Retiro entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Retiro entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('retiro'));
    }

    /**
     * Creates a form to delete a DatosEnvios entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('datosenvios_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /*========================= IMPRESION DE GUIA ======================================*/
    /**
     * Finds and displays a Guia entity.
     * @Pdf()
     */
    public function generarAction(){
        $request=$this->container->get('request_stack')->getCurrentRequest();
        $ids=$request->get('ids');
        $cont=0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal=array();
        $guia=new Guia();
        $arra=new ArrayCollection();
        $guia->setFechahora(new \DateTime('now'));
        foreach($ids as $id) {

            $cont++;
            if ($cont==1){
                $arrpare=array();
            }

            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);
            $guia->setCliente($entity->getCliente());
            $arra->add($entity);



        }

        $guia->setRetiros($arra);
        $em->persist($guia);
        $em->flush();


        $format = $this->get('request')->get('_format');

        $empresa = $this->container->getParameter('empresa');

        if($empresa=='fasttrack'){
            return $this->render(sprintf('PresisGuiaBundle:Guia:guia.%s.twig', $format), array(
                'entity' => $guia,
                'attr'=>array('target'=>'_blank'),
            ));
        }

        if($empresa=='maslogistica'){
            return $this->render(sprintf('PresisGuiaBundle:Guia:guiaMasLogistica.%s.twig', $format), array(
                'entity' => $guia,
                'attr'=>array('target'=>'_blank'),
            ));
        }

    }

    /**
     * Convierte las variables con "_" en arreglos
     *
     * @param $post
     * @return mixed
     */
    private function prepareEntities($post)
    {
        foreach ($post as $property => $value) {
            if (strpos($property, '_') !== false) {
                $exploded = explode('_', $property);
                $post[$exploded[0]][$exploded[1]] = $value;
                if (!isset($post[$exploded[0]]['id'])) $post[$exploded[0]]['id'] = '';
                unset($post[$property]);
            }
        }
        return $post;
    }

    /**
     * @param $vars
     * @param $cliente
     * @return int
     */
    private function calcularPeso($vars, $cliente, $empresa)
    {
        $dimensiones[0]['alto'] = $vars['alto'];
        $dimensiones[0]['largo'] = $vars['largo'];
        $dimensiones[0]['profundidad'] = $vars['ancho'];
        $peso = 0;

        $tipoFacturacion = ($cliente) ? $cliente->getTipoFacturacion() : 'peso';
        switch ($tipoFacturacion) {
            case 'peso':
                if (isset($vars['peso']) && $this->getPrecioManager()->validarPeso($vars['peso'])) {
                    $peso = $vars['peso'];
                }
                break;
            case 'volumen':
                if ($this->getPrecioManager()->validarDimensiones($dimensiones)) {
                    $peso = $this->getPrecioManager()->calcularPesoVolumetricoDomestico($vars['alto'], $vars['largo'], $vars['ancho'], $cliente->getAforo());
                }
                break;
            case 'pesovolumen':
                $peso = $this->getPrecioManager()->calcularMayorPesoDomestico($vars['bultos'], $vars['alto'], $vars['largo'], $vars['ancho'], $vars['peso'], $cliente->getAforo(),$empresa);
                break;
        }
        return $peso;
    }

    public function costosEcommerceAction($id){

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

        $entity->getDatosEnvios()->setCliente($entity->getCliente());
        //$entity->getDatosEnvios()->setValorDeclarado($entity->getValorDeclarado());
        //$entity->getDatosEnvios()->setCustodia($entity->getCustodia());
        //$entity->getDatosEnvios()->setSeguro($entity->getSeguro());
        //$entity->getDatosEnvios()->setCostoPorContrareembolso($entity->getCostoPorContrareembolso());
        //$entity->getDatosEnvios()->setFlete($entity->getFlete());
        //$entity->getDatosEnvios()->setMontoGuiaWeb($entity->getMontoGuiaWeb());
        //$entity->getDatosEnvios()->setCostoDespachoAExpreso($entity->getCostoDespachoAExpreso());
        //$entity->getDatosEnvios()->setCostoPorMonitoreoActivo($entity->getCostoPorMonitoreoActivo());
        //$entity->getDatosEnvios()->setCostoPorRemitoConforme($entity->getCostoPorRemitoConforme());
        //$entity->getDatosEnvios()->setCostoAdicional1($entity->getCostoAdicional1());
        //$entity->getDatosEnvios()->setCostoAdicional2($entity->getCostoAdicional2());
        //$entity->getDatosEnvios()->setCostoAdicional3($entity->getCostoAdicional3());
       // $entity->getDatosEnvios()->setPeso($entity->getPeso());
        $entity->getDatosEnvios()->setBultos($entity->getCantidad());
        $entity->getDatosEnvios()->setServicio($entity->getServicio());
        $em->persist($entity);
        $em->flush();

        $this->calcularCostos($entity);
        $this->calcularTiempoEnTransito($entity);

        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param $retiro
     */
    private function calcularCostos($retiro)
    {

        $de = $retiro->getDatosEnvios();
        if(!$de->getCliente()) {
            throw new Exception('Cliente no existe');
        }

        $empresa = $this->container->getParameter('empresa');

        //die("HOLA: ".$de->getValorDeclarado());
        //HOLA
        $de
            //->setValorDeclarado(0)
            ->setCustodia(0)
            ->setSeguro(0)
            ->setCostoPorContrareembolso(0)
            ->setFlete(0)
            ->setMontoGuiaWeb(0)
            ->setCostoDespachoAExpreso(0)
            ->setCostoPorMonitoreoActivo(0)
            ->setCostoPorRemitoConforme(0)
            ->setCostoAdicional1(0)
            ->setCostoAdicional2(0)
            ->setCostoAdicional3(0)
        ;

        if(!$de->getSeguro()) {
            $de->setSeguro($this->getPrecioManager()->calcularSeguro(
                $de->getValorDeclarado(),
                $de->getCliente())
            );
        }

        $vars['alto'] = $de->getAlto();
        $vars['ancho'] = $de->getAncho();
        $vars['largo'] = $de->getLargo();
        $vars['peso'] = $de->getPeso();
        $de->setVolumen($this->calcularPeso($vars, $de->getCliente(),$empresa));

        switch ($de->getCliente()->getTipoDeCobro()) {
            case "bulto":
                if(!$de->getBultos() || $de->getBultos()<=0){
                    throw new Exception('Falta el campo bultos');
                }
                if(!$de->getServicio()){
                    throw new Exception('Falta el campo servicio');
                }
                if(!$retiro->getSender()->getCp()){
                    throw new Exception('Falta el campo cp de origen');
                }
                if(!$retiro->getComprador()->getCp()){
                    throw new Exception('Falta el campo cp de destino');
                }
                $de->setFlete(
                    $this->getPrecioManager()->calcularFletePorBultos(
                        $de->getBultos(),
                        $de->getServicio()->getId(),
                        $retiro->getSender()->getCp(),
                        $retiro->getComprador()->getCp(),
                        $de->getCliente()));
                break;
            case "valordeclarado":
                if(!$de->getValorDeclarado() || $de->getValorDeclarado()<=0){
                    throw new Exception('Falta el campo valor declarado');
                }
                $de->setFlete(
                    $this->getPrecioManager()->calcularFletePorValorDeclarado(
                        $de->getValorDeclarado(),
                        $de->getCliente())
                );
                break;
            default:
                if($de->getCliente()->getId()!='197'){
                    if(!$de->getPeso() || $de->getPeso()<=0){
                        throw new Exception('Falta el campo peso');
                    }
                    if(!$de->getServicio()){
                        throw new Exception('Falta el campo servicio');
                    }
                    if(!$retiro->getSender()->getCp()){
                        throw new Exception('Falta el campo cp de origen');
                    }
                    if(!$retiro->getComprador()->getCp()){
                        throw new Exception('Falta el campo cp de destino');
                    }
                }
                $de->setFlete(
                    $this->getPrecioManager()->calcularFletePorPeso(
                        $de->getPeso(),
                        $de->getServicio()->getId(),
                        $retiro->getSender()->getCp(),
                        $retiro->getComprador()->getCp(),
                        $de->getCliente()
                    ));
        }

        if (!$de->getCostoPorContrareembolso() && $de->getContrareembolso() != 0) {
            if ($de->getCliente()->getIsPorcentaje()) {
                $costo = ($de->getCliente()->getContrareembolsoCheque() +
                        $de->getCliente()->getContrareembolsoEfectivo())
                    * $de->getContrareembolso() / 100;
            } else {
                $costo = $de->getCliente()->getContrareembolsoCheque() + $de->getCliente()->getContrareembolsoEfectivo();
            }
            $de->setCostoPorContrareembolso($costo);
        }
        if($de->getContrareembolso() == 0) {
            $de->setCostoPorContrareembolso(0);
        }

        $de->setMontoGuiaWeb($de->getCliente()->getMontoGuiaWeb());

        if(!$de->getCostoPorRemitoConforme()) {
            $de->setCostoPorRemitoConforme($de->getCliente()->getMontoServicio());
        }

        $de->calcularTotal();
    }


    public function verificarCPAction(Request $request) {

        $empresa = $this->container->getParameter('empresa');

        $post = $request->request->all();
        $em = $this->getDoctrine()->getManager();

        $cpCordon = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $post['cp']));

        if($cpCordon) {
            if($empresa=='maslogistica'){
                //26-03 SI ES MASLOGISTICA DEVUELVO LA SUB ZONA PARA SABER SI DEBO O NO COBRAR SEGURO
                return new Response($cpCordon->getSubZona());
            }
            if($empresa=='fasttrack'){
                return new Response($post['cp'] . " es un CP existente");
            }
            if($empresa=='caktus'){
                return new Response($post['cp'] . " es un CP existente");
            }
            //return new Response($post['cp'] . " es un CP existente");
        }

        return new Response($post['cp'] . " no es un CP existente", Response::HTTP_NOT_FOUND);
    }

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
        //$fecha = new \DateTime($retiro->getDatosEnvios()->getFecha()->format(\DateTime::ISO8601));
        $fecha = new \DateTime($retiro->getDatosEnvios()->getFecha()->format(\DateTime::ISO8601));
        $fecha->add(new \DateInterval('P'.$days.'D'));

        $fecha = $this->excluirFinDeSemana($retiro->getDatosEnvios()->getFecha()->format('U'), $fecha->format('U'));

        //23-01-2017 AGREGO PAARA TENER EN CUENTA LOS FINES DE SEMANA
        $dias = $this->esFinde($fecha);
        $fecha->add(new \DateInterval('P'.$dias.'D'));

        while($this->esFeriado($fecha)==1){
            $feriado = 1;
            $fecha->add(new \DateInterval('P'.$feriado.'D'));
            $dias = $this->esFinde($fecha);
            $fecha->add(new \DateInterval('P'.$dias.'D'));
        }

        $retiro->getDatosEnvios()->setFechaPactada($fecha);
    }
    /*============================CALCULA LA FINES DE SEMANA Y FERIADOS===================================*/
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
    /*=======================================================================*/
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

        if(!$cpCordon){
            throw new Exception('Ocurrio un problema con el cordon');
        }
        $cordon = $cpCordon->getCordon()->getDescripcion();
        return ($cordon != "1" && $cordon != "2");
    }

    private function downloadAsExcel($retiros)
    {

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("ePresis")
            ->setLastModifiedBy("ePresis")
            ->setTitle("Listado de guias");
        $phpExcelObject->setActiveSheetIndex(0);
        /* TODO! Hay que repensar todo este proceso: virtualproperties, campos exportables. No debe ser tan manual */
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "Nro. Guia");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Cliente");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, 1, "Servicio");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, 1, "Guia Manual");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(4, 1, "Guia Agente");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(5, 1, "Fecha");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(6, 1, "Estado");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(7, 1, "Detalle Entrega");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(8, 1, "F. Hora Estado");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(9, 1, "Distribuidor");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(10, 1, "Bultos");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(11, 1, "Peso");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(12, 1, "Alto");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(13, 1, "Largo");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(14, 1, "Ancho");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(15, 1, "Valor declarado");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(16, 1, "Contrareembolso");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(17, 1, "C. Origen");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(18, 1, "C. Destino");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(19, 1, "Zona");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(20, 1, "Nro. Planilla");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(21, 1, "F. Planilla");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(22, 1, "Observaciones");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(23, 1, "Flete");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(24, 1, "C P C");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(25, 1, "Seguro");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(26, 1, "Costo SRC");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(27, 1, "Costo D Expreso");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(28, 1, "Custodia");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(29, 1, "$ Guia WEB");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(30, 1, "C M Activo");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(31, 1, "Costo A 1");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(32, 1, "Costo A 2");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(33, 1, "Costo A 3");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(34, 1, "Total Flete");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(35, 1, "Fecha Pactada");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(36, 1, "Rem. Empresa");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(37, 1, "Rem. Nombre");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(38, 1, "Rem. Dirección");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(39, 1, "Rem. Localidad");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(40, 1, "Rem. Provincia");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(41, 1, "Rem. CP");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(42, 1, "Rem. Celular");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(43, 1, "Rem. Other Info");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(44, 1, "Com. Empresa");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(45, 1, "Com. Nombre");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(46, 1, "Com. Dirección");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(47, 1, "Com. Localidad");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(48, 1, "Com. Provincia");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(49, 1, "Com. CP");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(50, 1, "Com. Email");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(51, 1, "Com. Celular");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(52, 1, "Com. Other Info");

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(53, 1, "Detalle Estado");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(54, 1, "F. U. Estado");

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(55, 1, "Receptor Nombre");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(56, 1, "Receptor Apellido");
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(57, 1, "Receptorr DNI");

        foreach ($retiros as $fila => $retiro) {
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $fila+2, $retiro->getId());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $fila+2, $retiro->getCliente());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $fila+2, $retiro->getServicioNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, $fila+2, $retiro->getRemito());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(4, $fila+2, $retiro->getDatosEnvios()->getGuiaAgente());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(5, $fila+2, $retiro->getDatosEnvios()->getFecha());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(6, $fila+2, $retiro->getEstadoCodigo());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(7, $fila+2, $retiro->getDetalleEntrega());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(8, $fila+2, $retiro->getFechaHoraEntrega());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(9, $fila+2, $retiro->getDistribuidor());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(10, $fila+2, $retiro->getDatosEnvios()->getBultos());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(11, $fila+2, $retiro->getDatosEnvios()->getPeso());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(12, $fila+2, $retiro->getDatosEnvios()->getAlto());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(13, $fila+2, $retiro->getDatosEnvios()->getLargo());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(14, $fila+2, $retiro->getDatosEnvios()->getAncho());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(15, $fila+2, $retiro->getDatosEnvios()->getValorDeclarado());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(16, $fila+2, $retiro->getDatosEnvios()->getContrareembolso());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(17, $fila+2, $retiro->getDatosEnvios()->getCordonOrigen());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(18, $fila+2, $retiro->getDatosEnvios()->getCordonDestino());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(19, $fila+2, $retiro->getZona());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(20, $fila+2, $retiro->getNroPlanilla());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(21, $fila+2, $retiro->getFechaPlanilla());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(22, $fila+2, $retiro->getDatosEnvios()->getObservaciones());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(23, $fila+2, $retiro->getDatosEnvios()->getFlete());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(24, $fila+2, $retiro->getDatosEnvios()->getCostoPorContrareembolso());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(25, $fila+2, $retiro->getDatosEnvios()->getSeguro());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(26, $fila+2, $retiro->getDatosEnvios()->getCostoPorRemitoConforme());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(27, $fila+2, $retiro->getDatosEnvios()->getCostoDespachoAExpreso());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(28, $fila+2, $retiro->getDatosEnvios()->getCustodia());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(29, $fila+2, $retiro->getDatosEnvios()->getMontoGuiaWeb());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(30, $fila+2, $retiro->getDatosEnvios()->getCostoPorMonitoreoActivo());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(31, $fila+2, $retiro->getDatosEnvios()->getCostoAdicional1());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(32, $fila+2, $retiro->getDatosEnvios()->getCostoAdicional2());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(33, $fila+2, $retiro->getDatosEnvios()->getCostoAdicional3());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(34, $fila+2, $retiro->getDatosEnvios()->getTotalFlete());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(35, $fila+2, $retiro->getDatosEnvios()->getFechaPactada());

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(36, $fila+2, $retiro->getRemitenteEmpresa());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(37, $fila+2, $retiro->getRemitenteRemitente());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(38, $fila+2, $retiro->getDireccionRemitenteNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(39, $fila+2, $retiro->getLocalidadRemitenteNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(40, $fila+2, $retiro->getProvinciaRemitenteNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(41, $fila+2, $retiro->getCpRemitenteNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(42, $fila+2, $retiro->getCelularRemitenteNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(43, $fila+2, $retiro->getOtherInfoRemitenteNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(44, $fila+2, $retiro->getEmpresaCompradorGuia());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(45, $fila+2, $retiro->getComprador());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(46, $fila+2, $retiro->getDireccionComprador());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(47, $fila+2, $retiro->getLocalidadCompradorGuia());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(48, $fila+2, $retiro->getProvinciaCompradorGuia());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(49, $fila+2, $retiro->getCpCompradorGuia());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(50, $fila+2, $retiro->getEmailComprador());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(51, $fila+2, $retiro->getCelularCompradorGuia());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(52, $fila+2, $retiro->getOtherInfoCompradorGuia());

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(53, $fila+2, $retiro->getEstadoNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(54, $fila+2, $retiro->getFechaUltimoEstado());

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(55, $fila+2, $retiro->getReceptorNombre());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(56, $fila+2, $retiro->getReceptorApellido());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(57, $fila+2, $retiro->getDni());
        }
        $phpExcelObject->getActiveSheet()->setTitle('Guias');

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Guias Exportadas.xlsx'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Expires: 0');
        $response->headers->set('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        //$response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
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

    public function confirmPagoRetiroAction(Request $request)
    {
        $vars = $request->query->all();
        $em = $this->getDoctrine()->getManager();

        $fecha = date('d/m/Y');
        $fecha = \DateTime::createFromFormat('d/m/Y', $fecha);

        foreach($vars['ids'] as $id_retiro) {

            $estado = $em->getRepository('EstadoBundle:Estado')->find(41);

            $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);
            $retiro->getDatosEnvios()->setCobrado(true);
            /*$retiro->getDatosEnvios()->setFechaConfirmada($fecha);
            $retiro->setEstado($estado);
            $retiro->setFechaUltimoEstado($fecha);*/
            //$retiro->getDatosEnvios()->setDebeRetirarse(true);

            //AGREGAMOS EL REGGISTRO EN LA TABLA TRACKER
            $tracker = new Tracker();
            $tracker->setRetiro($retiro);
            $tracker->setEstado($retiro->getEstado());
            $tracker->setReceptorFechaHora($fecha);
            $tracker->setDetalles("ENVIO ABONADO");
            $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
            $em->persist($tracker);
            $em->flush();


        }
        $em->persist($retiro);
        $em->flush();

        return $this->redirect($this->generateUrl('datosenvios_pendientes_pago'));
        //return $this->redirect($this->generateUrl('datosenvios_imprimir_constancia'));
    }


    public function confirmRetiroAction(Request $request)
    {

        $empresa = $this->container->getParameter('empresa');

        $vars = $request->query->all();

        //die($vars['precinto']);

        $em = $this->getDoctrine()->getManager();

        $fecha = date('d/m/Y');
        $fecha = \DateTime::createFromFormat('d/m/Y', $fecha);

        foreach($vars['ids'] as $id_retiro) {

            $estado = $em->getRepository('EstadoBundle:Estado')->find(41);

            $solicitudRetiro = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->findOneBy(array("retiro"=>$id_retiro));

            if($solicitudRetiro){

                $fechaR = new \DateTime();
                $horaR = date("H:i");

                    if($horaR>'13:00'){
                        $fechaR->add(new \DateInterval('P1D'));
                        $fechaRetiro = $this->excluirFinDeSemana($fechaR->format('U'),$fechaR->format('U'));
                        $solicitudRetiro->setFecha($fechaRetiro);
                    }else{
                        $solicitudRetiro->setFecha($fecha);
                    }
                $solicitudRetiro->setConfirmada(true);
            }

            //die("HOLA: ".$solicitudRetiro->getRetiro());

            if($empresa=='maslogistica'){
                //die("Mas Logistica");
                $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);
                $retiro->getDatosEnvios()->setConfirmada(true);
                $retiro->getDatosEnvios()->setFechaConfirmada($fecha);
                $retiro->setEstado($estado);
                $retiro->setFechaUltimoEstado($fecha);
                //AGREGO PARA MAS LOGISTICA CUANDO CONFIRMA CAMBIA LA FECHA DE LA GUIA A LA FECHA DE CONFIRMACION
                $retiro->setFechHora($fecha);
                $retiro->getDatosEnvios()->setFecha($fecha);
                //AGREGO PARA CAMBIAR EL ESTADO DE GESTION CEL
                if($vars['trayecto']=="aMovistar"){
                    $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>'RF'));
                    $retiro->getGestionCel()->setEstado($estado);
                }
                if($vars['trayecto']=="aGalander"){
                    $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>'EDET'));
                    $retiro->getGestionCel()->setEstado($estado);
                }

                $retiro->getGestionCel()->setPrecinto($vars['precinto']);
            }
            if(($empresa=='caktus') || ($empresa=='fasttrack')){
                //die("Caktus and Fasttrack");
                $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);
                $retiro->getDatosEnvios()->setConfirmada(true);
                $retiro->getDatosEnvios()->setFechaConfirmada($fecha);
                $retiro->setEstado($estado);
                $retiro->setFechaUltimoEstado($fecha);
                //31-03-17 PICCINI AGREGO PARA QUE CAMBIE LA FECHA Y CALCULE LA FECHA PACTADA
                $retiro->setFechHora($fecha);
                $this->calcularTiempoEnTransito($retiro);
                $retiro->getDatosEnvios()->setFecha($fecha);
            }
            //$retiro->getDatosEnvios()->setDebeRetirarse(true);

            //AGREGAMOS EL REGGISTRO EN LA TABLA TRACKER
            $tracker = new Tracker();
            $tracker->setRetiro($retiro);
            $tracker->setEstado($retiro->getEstado());
            $tracker->setReceptorFechaHora($fecha);
            $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
            $em->persist($tracker);
            $em->flush();


        }
        $em->persist($retiro);
        $em->flush();

        $nroManifiesto = $this->generarManifiesto($vars['ids']);

        return new Response($nroManifiesto);
        //return $this->redirect($this->generateUrl('datosenvios_confirmar'));
        //return $this->redirect($this->generateUrl('datosenvios_imprimir_constancia'));
    }

    private function generarManifiesto(array $ids){

        $start = microtime(true);

        $em = $this->getDoctrine()->getManager();

        //GENERO UN REGISTRO NUEVO CON UN ID PARA AGREGARLO A CADA REMITO ASOCIADO
        $manifiestoCarga = new ManifiestoCarga();
        $em->persist($manifiestoCarga);
        $em->flush();

        foreach ($ids as $id) {
            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);
            $entity->setNroConstancia($manifiestoCarga->getId());
            $em->persist($entity);

            //AGREGAMOS EL REGGISTRO EN LA TABLA TRACKER
            $tracker = new Tracker();
            $tracker->setRetiro($entity);

            if($entity->getGestioncel()->getTrayecto()=="aMovistar"){
                $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>'RF'));
                $tracker->setEstado($estado);
            }
            if($entity->getGestioncel()->getTrayecto()=="aGalander"){
                $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>'EDET'));
                $tracker->setEstado($estado);
            }
            $fecha = date('d/m/Y');
            $fecha = \DateTime::createFromFormat('d/m/Y', $fecha);
            $tracker->setReceptorFechaHora($fecha);
            $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
            $tracker->setFechaPlanilla($fecha);
            $tracker->setSucursal($entity->getGestioncel()->getSucursal());
            $tracker->setDetalles('Precinto: '.$entity->getGestioncel()->getPrecinto());
            $tracker->setNroPlanilla($manifiestoCarga->getId());

            //DATOS PARA GENERAR EL ARCHIVO DE UNIR CUANDO ES UNA OD (ORDEN DE ENTREGA)
            $dirSender = $entity->getSender()->getCalle().''.$entity->getSender()->getAltura().''.$entity->getSender()->getPiso().''.$entity->getSender()->getDpto();
            $cpSender = $entity->getSender()->getCp();
            $locSender = $entity->getSender()->getLocalidad();
            $remitente = $entity->getSender()->getRemitente();
            $email = $entity->getSender()->getEmail();
            $cod_clie_dlv_OD = $entity->getSender()->getId();
            $nom_Clie_Dlv_OD = "MOVISTAR";

            //DATOS PARA GENERAR EL ARCHIVO DE UNIR CUANDO ES UNA OR (ORDEN DE RETIRO)
            $nom_Clie_Dlv_OR = "GALANDER";
            $cod_clie_dlv_OR = $entity->getComprador()->getId();
            $cpComprador = $entity->getComprador()->getCp();
            $locComprador = $entity->getComprador()->getLocalidad();
            $apenom = $entity->getComprador()->getApenom();
            $emailComp = $entity->getComprador()->getEmail();
            $cod_clie_dlv_OR = $entity->getComprador()->getId();
            $nom_Clie_Dlv_OR = "GALANDER";
            $dirComprador = $entity->getComprador()->getCalle().''.$entity->getComprador()->getAltura().''.$entity->getComprador()->getPiso().''.$entity->getComprador()->getDpto();

            $precinto = $entity->getGestioncel()->getPrecinto();
            $servicio = $entity->getDatosEnvios()->getServicio()->getDescripcion();

            $em->persist($tracker);
            $em->flush();

        }
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $manifiestoCarga->setUsuario($user);


        if($user->getCliente()->getEmpresa()){
            $manifiestoCarga->setCliente($user->getCliente()->getEmpresa());
        }else{
            $manifiestoCarga->setCliente("SISTEMA");
        }

        $manifiestoCarga->setFecha(new \Datetime());
        $manifiestoCarga->setSucursal($user->getSucursal()->getCodSuc());

        try{
            $fechaDocumento = $manifiestoCarga->getFecha()->format('Y-m-d');
            $fechaTitulo = date('Ymd');
            $horaTitulo = date('His');
            $file = fopen("../web/unir/archivo".$fechaTitulo.$horaTitulo.".txt","a");
            for($i=1; $i<=2; $i++){
                if(($cpSender>='1900')&&($cpSender!='2800')&&($cpSender!='6700')&&($cpSender!='7223')){
                    $isInterior = 7;
                }else{
                    $isInterior = 1;
                }
                if($entity->getGestioncel()->getTrayecto()=='MOVISTAR'){
                    if($i==1){
                        $txt = "1".";"."129".";"."129".";OR;".$manifiestoCarga->getId().";".$fechaDocumento.";".$precinto.";".$cod_clie_dlv_OD.";"."1".";".$nom_Clie_Dlv_OD.";".$dirSender.";".$cpSender.";".$locSender.";1;0;0;1;0;comentarios;".$servicio.";".$remitente.";".$email.";".$isInterior.";"."\r\n";
                    }else{
                        $txt = "1".";"."129".";"."129".";OD;".$manifiestoCarga->getId().";".$fechaDocumento.";".$precinto.";".$cod_clie_dlv_OR.";"."1".";".$nom_Clie_Dlv_OR.";".$dirComprador.";".$cpComprador.";".$locComprador.";1;0;0;1;0;comentarios;".$servicio.";".$apenom.";".$emailComp.";1;"."\r\n";
                    }
                }else{
                    if($i==1){
                        $txt = "1".";"."129".";"."129".";OD;".$manifiestoCarga->getId().";".$fechaDocumento.";".$precinto.";".$cod_clie_dlv_OR.";"."1".";".$nom_Clie_Dlv_OR.";".$dirComprador.";".$cpComprador.";".$locComprador.";1;0;0;1;0;comentarios;".$servicio.";".$apenom.";".$emailComp.";1;"."\r\n";
                    }else{
                        $txt = "1".";"."129".";"."129".";OR;".$manifiestoCarga->getId().";".$fechaDocumento.";".$precinto.";".$cod_clie_dlv_OD.";"."1".";".$nom_Clie_Dlv_OD.";".$dirSender.";".$cpSender.";".$locSender.";1;0;0;1;0;comentarios;".$servicio.";".$remitente.";".$email.";".$isInterior.";"."\r\n";
                    }
                }
                fwrite($file, $txt);
            }
            fclose($file);
            //ABRE EL DIRECTORIO Y MUEVE LOS ARCHIVOS DE "WEB/UNIR" A "WEB/PROCESADOS"
            if ($handle = opendir('../web/unir'))
            {
                // Add all files inside the directory
                while (false !== ($entry = readdir($handle)))
                {
                    if ($entry != "." && $entry != ".." && !is_dir('../web/unir/' . $entry))
                    {
                        rename ('../web/unir/' .$entry,"../web/procesados/".$entry);
                    }
                }
                closedir($handle);
            }
            $start = microtime(true);

            if('http://'.$_SERVER['HTTP_HOST']=='http://trackers.onlinegeotrack.com.ar'){
                //FTP DESARROLLO
                $ftp_server = "181.30.37.12";
                $ftp_user_name = "GalanderTest";
                $ftp_user_pass = "Galander2017!";
            }else{
                //FTP PRODUCCION
                $ftp_server = "181.30.37.12";
                $ftp_user_name = "GalanderProd";
                $ftp_user_pass = "Galander2017!";
            }

            if ($handle = opendir('../web/procesados'))
            {
                // Add all files inside the directory
                while (false !== ($entry = readdir($handle)))
                {
                    if ($entry != "." && $entry != ".." && !is_dir('../web/procesados/' . $entry))
                    {
                        $destination_file = $entry;
                        $source_file = '../web/procesados/'.$entry;

                        // conexión
                        $conn_id = ftp_connect($ftp_server);

                        // logeo
                        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

                        // conexión
                        if ((!$conn_id) || (!$login_result)) {
                            $accion =  "Conexión al FTP con errores! -  Intentando conectar a $ftp_server for user $ftp_user_name";
                            $archivo = "Error de conexion con ftp!";
                            $smsusuario = "ranval"; //usuario de SMS MASIVOS
                            $smsclave 	 = "mrcce5"; //clave de SMS MASIVOS
                            $smsnumero = '1132454428';
                            $smstexto = "ERRO AL CONECTAR CON FTP: ".$destination_file;
                            $smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($smsnumero) ."&TEXTO=". urlencode($smstexto) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=". urlencode($smsclave) );
                            exit;
                        } else {
                            $accion =  "Conectado a $ftp_server, for user $ftp_user_name";
                        }

                        ftp_pasv($conn_id, true);

                        // archivo a copiar/subir
                        $upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

                        // estado de subida/copiado
                        if (!$upload) {
                            $archivo = "Error al subir el archivo!";
                            $smsusuario = "ranval"; //usuario de SMS MASIVOS
                            $smsclave 	 = "mrcce5"; //clave de SMS MASIVOS
                            $smsnumero = '1132454428';
                            $smstexto = "ERRO AL SUBIR EL ARCHIVO: ".$destination_file;
                            $smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($smsnumero) ."&TEXTO=". urlencode($smstexto) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=". urlencode($smsclave) );
                            //echo "RESPUESTA: ".$smsrespuesta;
                            if(trim($smsrespuesta)=='OK'){
                                $entity->getGestioncel()->setSms(true);
                            }
                        } else {
                            $archivo = "Archivo $source_file se ha subido exitosamente a $ftp_server en $destination_file";
                            rename ($source_file,"../web/backunir/".$destination_file);
                            $archivo = "Exito al subir el archivo";
                            //$smsusuario = "ranval"; //usuario de SMS MASIVOS
                            //$smsclave 	 = "mrcce5"; //clave de SMS MASIVOS
                            //$smsnumero = '1132454428';
                            //$smstexto = "ARCHIVO: ".$destination_file." subido con exito.";
                            //$smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($smsnumero) ."&TEXTO=". urlencode($smstexto) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=". urlencode($smsclave) );

                        }

                        $fecha = (string) date("Y-m-d H:i:s");
                        $totalTimexDestino = (string) ((microtime(true) - $start) / 60); // Tiempo en minutos.
                        $totalTime = number_format($totalTimexDestino, 2, '.', '');
                        $reporte = $fecha . ' Time: ' . $totalTime . " min. - ".$accion." - ".$archivo."\r\n----------\r\n" ;
                        file_put_contents("log_runTime.txt", $reporte, FILE_APPEND);

                        // cerramos
                        ftp_close($conn_id);
                    }
                }
                closedir($handle);
            }

        }catch(Exception $e){
            $e->getMessage();
            return new Response($e);
        }


        $em->persist($manifiestoCarga);
        $em->flush();

        return $manifiestoCarga->getId();
    }


    public function formAuditoriaAction()
    {
        return $this->render('DatosEnviosBundle:DatosEnvios:auditoria.html.twig');
    }

    /*=================================================================================================*/
    public function pendientesBolsinMovistarAction(){

        $em = $this->getDoctrine()->getManager();

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $servMovi = $em->getRepository('MovistarBundle:ServiciosMovistar')->findAll();

        return $this->render('DatosEnviosBundle:DatosEnvios:pendientes-de-envio-movistar.html.twig', array(
            'entities' => $user->getCliente()->getServicios(),
            'servMovi' => $servMovi,
        ));
    }

    public function getConfirmarBolsinAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();

        $attrs = $request->request->all();

        //die($attrs['servicioMaslo'].'*'.$attrs['servicioMovi']);

        $query->select('r,c,de')
            ->from('PresisRetiroBundle:Retiro','r')
            ->join('r.gestioncel','c')
            ->join('r.datosEnvios','de')
            ->where('r.gestioncel = c')
            ->andWhere('r.datosEnvios = de')
            ->andWhere('c.estado = 165');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if ($attrs['servicioMovi']) {
            $query->andWhere("c.tiposervicio = :servicioMovi")
                ->setParameter('servicioMovi', trim($attrs['servicioMovi']));
            unset($attrs["servicioMovi"]);
        }

        if ($attrs['servicioMaslo']) {
            $servicio = $em->getRepository("PresisServicioBundle:Servicio")->find($attrs['servicioMaslo']);
            //die('hola'.$servicio->getId());
            $query->andWhere("de.servicio = :servicioMaslo")
                ->setParameter('servicioMaslo', $servicio);
            unset($attrs["servicioMaslo"]);
        }

        if($user->hasRole('ROLE_BACK_OFFICE')){
            $query->andWhere("c.sucursal = :sucursal")
                ->setParameter('sucursal', trim($user->getSucursal()->getCodSuc()));
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    //FORMULARIO PARA INFORMAR LOS REPARADOS PARA CLIENTE GALANDER / MOVISTAR
    public function informarReparadosAction(Request $request){


        $entity = new GestionCel();

        $form = $this->createForm(new FinGestionCelType(), $entity, array(
            'method' => 'POST',
        ));

        return $this->render('DatosEnviosBundle:DatosEnvios:informar-reparados-galander.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

        /*$gestionCel = new GestionCel();
        $form = $this->createForm(FinGestionCelType::class, $gestionCel);

        return $this->render('DatosEnviosBundle:DatosEnvios:informar-reparados-galander.html.twig', array(
            'form'=>$form
        ));*/
    }

    //FORMULARIO PARA INFORMAR LOS REPARADOS PARA CLIENTE GALANDER / MOVISTAR
    public function informarFinalMovistarAction(){

        $em = $this->getDoctrine()->getManager();

        $estado = $em->getRepository('EstadoBundle:Estado')->findBy(array('codigo' => array('TOCEC','EEC')));

        return $this->render('DatosEnviosBundle:DatosEnvios:informar-estados-movistar.html.twig',array(
            'estados' => $estado,
        ));

    }

    public function saveGestionAction(Request $request)
    {
        return $this->render('PresisRetiroBundle:Retiro:index.html.twig', array(
            'nada' => "hola",
        ));
    }

    public function updateGestionAction(){

        $posted_values = $this->get('request')->request->all();

        $em = $this->getDoctrine()->getManager();


        $gestion = $em->getRepository('GestionCelBundle:GestionCel')->findOneBy(array('nroserie'=>$posted_values['imei']));

        if(!$gestion){
            return new Response("1");
        }

        $gestion->setEstadointervencion($posted_values["presis_gestioncelbundle_gestioncel"]["estadointervencion"]);
        $gestion->setCertificadoreparador($posted_values["presis_gestioncelbundle_gestioncel"]["certificadoreparador"]);
        $gestion->setPlacaswap($posted_values["presis_gestioncelbundle_gestioncel"]["placaswap"]);
        if(isset($posted_values["presis_gestioncelbundle_gestioncel"]["nroimei"])){
            $gestion->setNroimei($posted_values["presis_gestioncelbundle_gestioncel"]["nroimei"]);
        }
        $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo'=>'RF'));
        $gestion->setEstado($estado);
        $gestion->setTrayecto("aMovistar");
        $em->persist($gestion);

        $tracker = new Tracker();
        $fecha = date('d/m/Y');
        $fecha = \DateTime::createFromFormat('d/m/Y', $fecha);
        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('gestioncel'=>$gestion));
        $tracker->setRetiro($retiro);
        $tracker->setReceptorFechaHora($fecha);
        $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
        $tracker->setFechaPlanilla($fecha);
        $tracker->setSucursal($gestion->getSucursal());
        $tracker->setEstado($estado);
        $em->persist($tracker);
        $em->flush();

        return new Response("2");
    }

    public function pendientesBolsinGalanderAction(){

        $em = $this->getDoctrine()->getManager();

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $servMovi = $em->getRepository('MovistarBundle:ServiciosMovistar')->findAll();

        $sucursales = $em->getRepository('PresisGeneralBundle:Sucursal')->findBy(array('cliente'=>'13'));

        return $this->render('DatosEnviosBundle:DatosEnvios:pendientes-de-envio-galander.html.twig', array(
            'entities' => $user->getCliente()->getServicios(),
            'servMovi' => $servMovi,
            'sucursales' => $sucursales,
        ));
    }

    public function getConfirmarBolsinGalanderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();

        $attrs = $request->request->all();

        //die($attrs['servicioMaslo'].'*'.$attrs['servicioMovi'].'*'.$attrs['sucursal']);

        $query->select('r,c,de')
            ->from('PresisRetiroBundle:Retiro','r')
            ->join('r.gestioncel','c')
            ->join('r.datosEnvios','de')
            ->where('r.gestioncel = c')
            ->andWhere('r.datosEnvios = de')
            ->andWhere('c.estado = 160');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if ($attrs['sucursal']) {
            $query->andWhere("c.sucursal = :sucursal")
                ->setParameter('sucursal', trim($attrs['sucursal']));
            unset($attrs["sucursal"]);
        }

        if ($attrs['servicioMovi']) {
            $query->andWhere("c.tiposervicio = :servicioMovi")
                ->setParameter('servicioMovi', trim($attrs['servicioMovi']));
            unset($attrs["servicioMovi"]);
        }

        if ($attrs['servicioMaslo']) {
            $servicio = $em->getRepository("PresisServicioBundle:Servicio")->find($attrs['servicioMaslo']);
            //die('hola'.$servicio->getId());
            $query->andWhere("de.servicio = :servicioMaslo")
                ->setParameter('servicioMaslo', $servicio);
            unset($attrs["servicioMaslo"]);
        }



        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    public function pendientesDeInformeGalanderAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();

        $attrs = $request->request->all();

        $query->select('r,c,de')
            ->from('PresisRetiroBundle:Retiro','r')
            ->join('r.gestioncel','c')
            ->join('r.datosEnvios','de')
            ->where('r.gestioncel = c')
            ->andWhere('r.datosEnvios = de')
            ->andWhere('c.estadointervencion is null');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    public function exportarAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('GestionCelBundle:GestionCel')
            ->createQueryBuilder('e')
            ->getQuery();

        $result = $query->getResult();
        /*ladybug_dump($result);
        die();*/

        // solicitamos el servicio 'phpexcel' y creamos el objeto vacío...
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        // ...y le asignamos una serie de propiedades
        $phpExcelObject->getProperties()
            ->setCreator("Vabadus")
            ->setLastModifiedBy("Vabadus")
            ->setTitle("Ejemplo de exportación")
            ->setSubject("Ejemplo")
            ->setDescription("Listado de ejemplo.")
            ->setKeywords("vabadus exportar excel ejemplo");

        // establecemos como hoja activa la primera, y le asignamos un título
        $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->setTitle('Ejemplo de exportación');

        // escribimos en distintas celdas del documento el título de los campos que vamos a exportar
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'USUARIO')
            ->setCellValue('C1', 'ANI')
            ->setCellValue('D1', 'NRO. DE  SERIE')
            ->setCellValue('E1', 'APELLIDO Y NOMBRE')
            ->setCellValue('F1', 'NRO SST')
            ->setCellValue('G1', 'ACEPTA CARGOS')
            ->setCellValue('H1', 'NIVEL REPARACION')
            ->setCellValue('I1', 'MULETO')
            ->setCellValue('J1', 'EMEI MULETO')
            ->setCellValue('K1', 'FECHA ACT.')
            ->setCellValue('L1', 'FECHA FABRICACION')
            ->setCellValue('M1', 'FABRICANTE')
            ->setCellValue('N1', 'MODELO')
            ->setCellValue('O1', 'ORIGEN DEL EQUIPO')
            ->setCellValue('P1', 'SVA')
            //->setCellValue('Q1', 'FALLA')
            ->setCellValue('R1', 'ROTURA')
            //->setCellValue('S1', 'COMPLETITUD')
            ->setCellValue('T1', 'ESTADO INT.')
            ->setCellValue('U1', 'CERTIFICADO REP.')
            ->setCellValue('V1', 'PACA SWAP')
            ->setCellValue('W1', 'NUEVO IMEI')
            ->setCellValue('X1', 'TIPO CLIENTE')
            ->setCellValue('Y1', 'TIPO SERVICIO')
            ->setCellValue('Z1', 'CLAIM ASSURANT')
            ->setCellValue('AA1', 'ESTADO')
            ->setCellValue('AB1', 'PRECINTO')
            ->setCellValue('AC1', 'SUCURSAL')
            ->setCellValue('AD1', 'TRAYECTO')
        ;

        // fijamos un ancho a las distintas columnas
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('B')
            ->setWidth(30);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('C')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('D')
            ->setWidth(15);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('E')
            ->setWidth(20);

        // recorremos los registros obtenidos de la consulta a base de datos escribiéndolos en las celdas correspondientes
        $row = 3;
        foreach ($result as $item) {

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item->getId())
                ->setCellValue('B'.$row, $item->getUsuario())
                ->setCellValue('C'.$row, $item->getAni())
                ->setCellValue('D'.$row, $item->getNroserie())
                ->setCellValue('E'.$row, $item->getNomyape())
                ->setCellValue('F'.$row, $item->getNrosst())
                ->setCellValue('G'.$row, $item->getAceptaCargos())
                ->setCellValue('H'.$row, $item->getNivelderep())
                ->setCellValue('I'.$row, $item->getMuleto())
                ->setCellValue('J'.$row, $item->getImeimuleto())
                ->setCellValue('K'.$row, $item->getFechaactivacion())
                ->setCellValue('L'.$row, $item->getFechafabricacion())
                ->setCellValue('M'.$row, $item->getFabricante())
                ->setCellValue('N'.$row, $item->getModelo())
                ->setCellValue('O'.$row, $item->getOrigendelequipo())
                ->setCellValue('P'.$row, $item->getSva())
                //->setCellValue('Q'.$row, $item->getFalla())
                ->setCellValue('R'.$row, $item->getRotura())
                //->setCellValue('S'.$row, $item->getCompletitud())
                ->setCellValue('T'.$row, $item->getEstadointervencion())
                ->setCellValue('U'.$row, $item->getCertificadoreparador())
                ->setCellValue('V'.$row, $item->getPlacaswap())
                ->setCellValue('W'.$row, $item->getNroimei())
                ->setCellValue('X'.$row, $item->getTipocliente())
                ->setCellValue('Y'.$row, $item->getTiposervicio())
                ->setCellValue('Z'.$row, $item->getClaimassurant())
                ->setCellValue('AA'.$row, $item->getEstado())
                ->setCellValue('AB'.$row, $item->getPrecinto())
                ->setCellValue('AC'.$row, $item->getSucursal())
                ->setCellValue('AD'.$row, $item->getTrayecto())
            ;

            $row++;
        }

        // se crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'ejemplo.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    //VERIFICA SI UN NRO DE SERIE  / IMEI YA FUE REGISTRADO EN EL DIA
    public function nroSerieFindAction(Request $request){

        $nroSerie_id = $request->get("nroSerie_id");

        $fecha = new \DateTime();

        //die($nroSerie_id.'*'.$fecha->format('Y-m-d'));

        $em = $this->getDoctrine()->getManager();

        $existe = $em->getRepository('GestionCelBundle:GestionCel')->findOneBy(array('nroserie'=>$nroSerie_id,'fechaBase'=>$fecha));

        if($existe){
            return new Response(1);
        }
        return new Response(-1);
    }

    public function findGestionAction(Request $request){

        $imei = $request->query->get('imei');

        $em = $this->getDoctrine()->getManager();

        $gestiones = $em->getRepository('GestionCelBundle:GestionCel')->findOneBy(array('nroserie' => $imei));

        if (!$gestiones) {
            return new Response("No hay disponible ninguna gestion con el imei ingresado: $imei", Response::HTTP_CONFLICT);
        }

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($gestiones, "json"));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    //ACTUALIZA EL ESTADO DE UNA GESTION EN SU ETAPA FINAL, CUANDO LLEGA A MOVISTAR
    public function updateGestionMovistarAction(){

        $posted_values = $this->get('request')->request->all();


        $em = $this->getDoctrine()->getManager();


        $gestion = $em->getRepository('GestionCelBundle:GestionCel')->findOneBy(array('nroserie'=>$posted_values['imei']));

        if(!$gestion){
            return new Response("1");
        }

        $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('id'=>$posted_values['estado']));
        $gestion->setEstado($estado);


        $tracker = new Tracker();
        $fecha = date('d/m/Y');
        $fecha = \DateTime::createFromFormat('d/m/Y', $fecha);
        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('gestioncel'=>$gestion));
        $tracker->setRetiro($retiro);
        $tracker->setReceptorFechaHora($fecha);
        $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
        $tracker->setFechaPlanilla($fecha);
        $tracker->setSucursal($gestion->getSucursal());
        $tracker->setEstado($estado);
        $retiro->setEstado($estado);

        /*if($retiro->getDatosEnvios()->getServicio()->getCodServ()=="AR" || $retiro->getDatosEnvios()->getServicio()->getCodServ()=="IR"){
            $smsusuario = "ranval"; //usuario de SMS MASIVOS
            $smsclave 	 = "mrcce5"; //clave de SMS MASIVOS
            $smsnumero = $gestion->getAni();
            $smstexto = "Estimado: su equipo está disponible en sucursal para su retiro, orden nro: ".$retiro->getId();
            $smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($smsnumero) ."&TEXTO=". urlencode($smstexto) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=". urlencode($smsclave) );
            $gestion->setSms(utf8_encode($smsrespuesta));
        }else{
            $gestion->setSms("Tipo de srvicio sin sms disponible");
        }*/
        $em->persist($retiro);
        $em->persist($gestion);
        $em->persist($tracker);
        $em->flush();

        return new Response("2");
    }

    public function searchGestionAction(){

        $empresa = $this->container->getParameter('empresa');

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        //die("HOLA".$user->getSucursal());

        if($user->hasRole('ROLE_BACK_OFFICE')){
            $sucursales = $em->getRepository('PresisGeneralBundle:Sucursal')->findBy(array('cliente'=>'13','descripcion'=>trim($user->getSucursal())));
        }else{
            $sucursales = $em->getRepository('PresisGeneralBundle:Sucursal')->findBy(array('cliente'=>'13'));
        }

        $estados = $em->getRepository('EstadoBundle:Estado')->findBy(
            array(
                'codigo'=>array('EDET','ETHR','IH','RF','RCEC','ERCEC','TOCEC','EEC','NRCEC')
            ));

        $entity = new GestionCel();

        $token = md5('matias'.date('Y-m-d H:i:s'));

        $form = $this->createForm(new SearchGestionCelType(), $entity, array(
            'action' => $this->generateUrl('datosenvios_search_gestion_cel'),
            'method' => 'POST',
        ));

        return $this->render('DatosEnviosBundle:DatosEnvios:search-gestioncel.html.twig', array(
            'entity' => $entity,
            'sucursales' => $sucursales,
            'sucursal' => $user->getSucursal(),
            'estados' => $estados,
            'token' => $token,
            'form'   => $form->createView(),
        ));
    }

    //BUSCAR GUIA WEB
    public function findGestionCelAction(Request $request)

    {
        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $attrs = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();
        $query->select('r')
            ->from('GestionCelBundle:GestionCel','r');

        if($attrs["ani"]) {
            $query->andWhere("r.ani = :ani")
                ->setParameter('ani', $attrs["ani"]);
            unset($attrs["ani"]);
        }

        if($attrs["nroserie"]) {
            $query->andWhere("r.nroserie = :nroserie")
                ->setParameter('nroserie', $attrs["nroserie"]);
            unset($attrs["nroserie"]);
        }

        if($attrs["nomyape"]) {
            $query->andWhere("r.nomyape = :nomyape")
                ->setParameter('nomyape', $attrs["nomyape"]);
            unset($attrs["nomyape"]);
        }

        if($attrs["nrosst"]) {
            $query->andWhere("r.nrosst = :nrosst")
                ->setParameter('nrosst', $attrs["nrosst"]);
            unset($attrs["nrosst"]);
        }

        if($attrs["fabricante"]) {
            $query->andWhere("r.fabricante = :fabricante")
                ->setParameter('fabricante', $attrs["fabricante"]);
            unset($attrs["fabricante"]);
        }

        if($attrs["modelo"]) {
            $query->andWhere("r.modelo = :modelo")
                ->setParameter('modelo', $attrs["modelo"]);
            unset($attrs["modelo"]);
        }

        if($attrs["estadointervencion"]) {
            $query->andWhere("r.estadointervencion = :estadointervencion")
                ->setParameter('estadointervencion', $attrs["estadointervencion"]);
            unset($attrs["estadointervencion"]);
        }

        if($attrs["nroimei"]) {
            $query->andWhere("r.nroimei = :nroimei")
                ->setParameter('nroimei', $attrs["nroimei"]);
            unset($attrs["nroimei"]);
        }

        if($attrs["estado"]) {
            $query->andWhere("r.estado = :estado")
                ->setParameter('estado', $attrs["estado"]);
            unset($attrs["estado"]);
        }

        if($attrs["origendelequipo"]) {
            $query->andWhere("r.origendelequipo = :origendelequipo")
                ->setParameter('origendelequipo', $attrs["origendelequipo"]);
            unset($attrs["origendelequipo"]);
        }

        if($attrs["sva"]) {
            $query->andWhere("r.sva = :sva")
                ->setParameter('sva', $attrs["sva"]);
            unset($attrs["sva"]);
        }


        if($attrs["rotura"]) {
            $query->andWhere("r.rotura = :rotura")
                ->setParameter('rotura', $attrs["rotura"]);
            unset($attrs["rotura"]);
        }


        if($attrs["tipocliente"]) {
            $query->andWhere("r.tipocliente = :tipocliente")
                ->setParameter('tipocliente', $attrs["tipocliente"]);
            unset($attrs["tipocliente"]);
        }


        if($attrs["tiposervicio"]) {
            $query->andWhere("r.tiposervicio = :tiposervicio")
                ->setParameter('tiposervicio', $attrs["tiposervicio"]);
            unset($attrs["tiposervicio"]);
        }


        if($attrs["claimassurant"]) {
            $query->andWhere("r.claimassurant = :claimassurant")
                ->setParameter('claimassurant', $attrs["claimassurant"]);
            unset($attrs["claimassurant"]);
        }


        if($attrs["certificadoreparador"]) {
            $query->andWhere("r.certificadoreparador = :certificadoreparador")
                ->setParameter('certificadoreparador', $attrs["certificadoreparador"]);
            unset($attrs["certificadoreparador"]);
        }

        if($attrs["placaswap"]) {
            $query->andWhere("r.placaswap = :placaswap")
                ->setParameter('placaswap', $attrs["placaswap"]);
            unset($attrs["placaswap"]);
        }

        if($attrs["codSuc"]) {
            $query->andWhere("r.sucursal = :codSuc")
                ->setParameter('codSuc', trim($attrs["codSuc"]));
            unset($attrs["codSuc"]);
        }

        if($attrs["fechaDesde"]) {
            $attrs["fechaDesde"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaDesde"]);
            $query->andWhere('r.fechaBase >= :desde')
                ->setParameter('desde', $attrs["fechaDesde"]->format('Y-m-d 00:00:00'));
            unset($attrs["fechaDesde"]);
        }

        if($attrs["fechaHasta"]) {
            $attrs["fechaHasta"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaHasta"]);
            $query->andWhere('r.fechaBase <= :hasta')
                ->setParameter('hasta', $attrs["fechaHasta"]->format('Y-m-d 23:59:59'));
            unset($attrs["fechaHasta"]);
        }

        $dontPaginate = true;
        if ($attrs["limite"] > 0) {
            $query->setFirstResult($attrs["pagina"] * $attrs["limite"])
                ->setMaxResults($attrs["limite"]);
            $dontPaginate = false;
        }
        unset($attrs["pagina"]);
        unset($attrs["limite"]);


        foreach ($attrs as $key => $value) {
            if (trim($value) !== '') {
                $query->andWhere("r.$key = :$key")
                    ->setParameter("$key", $value);
            }
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result["total"] = count($paginator);
        $result["rows"] = $paginator->getQuery()->getResult();

        if ($dontPaginate) {
            return $this->downloadAsExcel($result["rows"]);
        } else {
            $serializer = $this->get('jms_serializer');
            $json = $serializer->serialize($result, "json");

            return new Response($json);
        }
    }

}

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class MyCustomHydrator extends DoctrineObject {
    protected function handleTypeConversions($value, $typeOfField) {
        if($typeOfField == 'datetime'){
            $cantidad = strlen($value);
            if($cantidad==8){
                return \DateTime::createFromFormat('d/m/y', $value);
            }else{
                return \DateTime::createFromFormat('d/m/Y', $value);
            }
        }
        return parent::handleTypeConversions($value, $typeOfField);
    }
}