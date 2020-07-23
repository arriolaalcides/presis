<?php

namespace Presis\RecorridoBundle\Controller;

use Presis\RecorridoBundle\Entity\RecorridoRetiro;
use Presis\RetiroBundle\PresisRetiroBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\RecorridoBundle\Entity\Recorrido;
use Presis\EstadoBundle\Entity\Estado;
use Presis\RecorridoBundle\Form\RecorridoType;
use Symfony\Component\HttpFoundation\Response;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\TrackerBundle\Entity\Tracker;
use Ps\PdfBundle\Annotation\Pdf;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Recorrido controller.
 *
 */
class RecorridoController extends Controller
{

    /**
     * Todos los recorridos (Paginado)
     *
     */
    public function asAjaxAction(Request $request)
    {
        $fechaDesde = $request->request->get('desde');
        $fechaHasta = $request->request->get('hasta');
        $id_distribuidor = $request->request->get('distribuidor');
        $id_expreso = $request->request->get('expreso');

        $nroPlanilla = $request->request->get('nroPlanilla');
        $colectora = $request->request->get('colectora');
        $guiaExpreso = $request->request->get('guiaExpreso');

        /*$offset       = $request->request->get('page');
        $offset = (!$offset)? 0: $offset-1;
        $limit      = $request->request->get('rows');
        if(!$limit) $limit = 10;
        $sort      = $request->request->get('sort');
        $order      = $request->request->get('order');*/

        $limite = $request->request->get('limite');
        $pagina = $request->request->get('pagina');


        $this->borrarRecorridosSinRetiros();

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository("RecorridoBundle:Recorrido")->createQueryBuilder('r');

        if($fechaDesde) {
            $fechaDesde = \DateTime::createFromFormat('d/m/Y', $fechaDesde);
            $entities->andWhere('r.fecha >= :desde')
                ->setParameter('desde', $fechaDesde->format('Y-m-d'));
        }

        if($fechaHasta) {
            $fechaHasta = \DateTime::createFromFormat('d/m/Y', $fechaHasta);
            $entities->andWhere('r.fecha <= :hasta')
                ->setParameter('hasta', $fechaHasta->format('Y-m-d'));
        }

        if($id_expreso) {
            $entities->andWhere('r.expreso = :expreso')
                ->setParameter('expreso', $id_expreso);
        }

        if($nroPlanilla) {
            $entities->andWhere('r.id = :nroPlanilla')
                ->setParameter('nroPlanilla', $nroPlanilla);
        }
        if($guiaExpreso) {
            $entities->andWhere('r.guiaExpreso = :guiaExpreso')
                ->setParameter('guiaExpreso', $guiaExpreso);
        }
        if($colectora) {
            $entities->andWhere('r.colectora = :colectora')
                ->setParameter('colectora', $colectora);
        }

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        /*if($user->hasRole('ROLE_DISTRIBUIDOR') && $user->getDistribuidor()) {
            $id_distribuidor = $user->getDistribuidor()->getId();
        }*/

        if($id_distribuidor) {
            $entities->andWhere('r.distribuidor = :distribuidor')
                ->setParameter('distribuidor', $id_distribuidor);
        }

        /*if ($offset)
            $entities->setFirstResult($offset * $limit);

        if ($limit)
            $entities->setMaxResults($limit);*/

            $entities->setFirstResult($pagina * $limite)
                ->setMaxResults($limite);



        /*if($sort && $order)
            $entities->orderBy("r.$sort", $order);*/

        $paginator = new Paginator($entities, $fetchJoinCollection = false);
        $result["total"] = count($paginator);
        $result["rows"] = $entities->getQuery()
            ->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    public function borrarRecorridosSinRetiros() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('RecorridoBundle:Recorrido')->findAll();

        foreach ($entities as $recorrido) {
            if($recorrido->getCantidadRetiros() == 0) {
                $em->remove($recorrido);
                $em->flush();
            }
        }
    }

    /**
     * Todas las planillas
     *
     */
    public function indexAction()
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $distribuidor = ($user->hasRole('ROLE_DISTRIBUIDOR'))? $user->getDistribuidor() : null;

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        return $this->render('RecorridoBundle:Recorrido:index.html.twig', array(
            'distribuidor' => $distribuidor,
            'sucursal' => $sucursal,
        ));
    }

    /**
     * Returns all Retiros that belongs to a Recorrido
     *
     */
    public function retirosAsAjaxAction()
    {
        $recorridoId = $this->get('request')->request->get('recorrido');

        $json = '[]';
        if($recorridoId != ''){
            $em = $this->getDoctrine()->getManager();

            $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($recorridoId);

            if($recorrido) {

                $rr = $recorrido->getRecorridosRetiros();
                if($rr->count() > 0) {
                    $serializer = $this->get('jms_serializer');
                    $json = $serializer->serialize($rr, "json");
                }
            }
        }

        return new Response('{"data":'.$json."}");
    }

    /**
     * Creates a new Recorrido entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Recorrido();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('RecorridoBundle:Recorrido:new-edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'editing' => true,
        ));
    }

    /**
     * Creates a form to create a Recorrido entity.
     *
     * @param Recorrido $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Recorrido $entity)
    {

        $securityContext = $this->container->get('security.context');

        $user = $this->get('security.context')->getToken()->getUser();

        /*ladybug_dump($securityContext);
        die();*/

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        //die("SUCURSAL: ".$sucursal);

        $form = $this->createForm(new RecorridoType($securityContext), $entity, array(
            'action' => $this->generateUrl('recorrido_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'NUEVA PLANILLA'));

        return $form;
    }

    /**
     * Displays a form to create a new Recorrido entity.
     *
     */
    public function newAction()
    {
        $entity = new Recorrido();
        $form   = $this->createCreateForm($entity);

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        return $this->render('RecorridoBundle:Recorrido:new-edit.html.twig', array(
            'entity' => $entity,
            'sucursal' => $sucursal,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showAction($id=null)
    {
        //$id = 11;
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecorridoBundle:Recorrido')->find($id);

        $empresa = $this->container->getParameter('empresa');

        if($empresa=='caktus' || $empresa=='maslogistica'){

            if($entity->getesEntrega()==true){
                $em = $this->getDoctrine()->getManager();
                $repository = $this->getDoctrine()
                    ->getRepository('PresisRetiroBundle:Retiro');
                $query = $repository->createQueryBuilder('r')
                    ->join('r.comprador', 'c')
                    ->where('r.nroPlanilla =:id')
                    ->addOrderBy('c.localidad', 'ASC')
                    ->addOrderBy('c.calle', 'ASC')
                    ->setParameter('id', $id)
                    ->getQuery();
                $retirosss = $query->getResult();
            }else{
                /*$query = $em->createQuery(
                    'SELECT r.id, rr.orden
                     FROM 
                     RecorridoBundle:Recorrido r,
                     RecorridoBundle:RecorridoRetiro rr
                     WHERE
                     rr.recorrido = r AND
                     r.id = :id
                     ');
                $query->setParameter('id', '250');
                $paginator = new Paginator($query, $fetchJoinCollection = false);
                $retirosss = $paginator->getQuery()->getResult();*/
                /*===================================================================*/
                /*$recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($id);
                $rr = $recorrido->getRecorridosRetiros();
                $retirosss = $rr->getValues();*/
                /*===================================================================*/
                $em = $this->getDoctrine()->getManager();
                $repository = $this->getDoctrine()
                    ->getRepository('PresisRetiroBundle:Retiro');
                $query = $repository->createQueryBuilder('r')
                    ->join('r.sender', 's')
                    ->where('r.nroPlanilla =:id')
                    ->addOrderBy('s.otherInfo', 'ASC')
                    ->addOrderBy('s.calle', 'ASC')
                    ->setParameter('id', $id)
                    ->getQuery();
                $retirosss = $query->getResult();
            }
        }
        if($empresa=='fasttrack'){
            if($entity->getesEntrega()==true){
                $em = $this->getDoctrine()->getManager();
                $repository = $this->getDoctrine()
                    ->getRepository('PresisRetiroBundle:Retiro');
                $query = $repository->createQueryBuilder('r')
                    ->join('r.comprador', 'c')
                    ->where('r.nroPlanilla =:id')
                    ->addOrderBy('c.localidad', 'ASC')
                    ->addOrderBy('c.calle', 'ASC')
                    ->setParameter('id', $id)
                    ->getQuery();
                $retirosss = $query->getResult();
            }else{
                $em = $this->getDoctrine()->getManager();
                $repository = $this->getDoctrine()
                    ->getRepository('PresisRetiroBundle:Retiro');
                $query = $repository->createQueryBuilder('r')
                    ->join('r.sender', 's')
                    ->where('r.nroPlanilla =:id')
                    ->addOrderBy('s.otherInfo', 'ASC')
                    ->addOrderBy('s.calle', 'ASC')
                    ->setParameter('id', $id)
                    ->getQuery();
                $retirosss = $query->getResult();
            }
        }

        $format = $this->get('request')->get('_format');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recorrido entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

       //$empresa = $this->container->getParameter('empresa');
        if($empresa=='maslogistica'){
            return $this->render(sprintf('RecorridoBundle:Recorrido:planiMasLogistica.%s.twig', $format), array(
                'recorrido' => $entity,
                'retiros'   => $retirosss,
                'attr'      => array('target'=>'_blank'),
            ));
        }
        if($empresa=='caktus'){
            return $this->render(sprintf('RecorridoBundle:Recorrido:planiCaktus.%s.twig', $format), array(
                'recorrido' => $entity,
                'retiros' => $retirosss,
                'attr'      => array('target'=>'_blank'),
            ));
        }
        if($empresa=='fasttrack'){
            return $this->render(sprintf('RecorridoBundle:Recorrido:show.%s.twig', $format), array(
                'recorrido' => $entity,
                'retiros'   => $retirosss,
                'attr'      => array('target'=>'_blank'),
            ));
        }

    }

    /**
     * Displays a form to edit an existing Recorrido entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecorridoBundle:Recorrido')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recorrido entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        return $this->render('RecorridoBundle:Recorrido:new-edit.html.twig', array(
            'entity'      => $entity,
            'sucursal' => $sucursal,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'editing' => true,
        ));
    }

    /**
     * Creates a form to edit a Recorrido entity.
     *
     * @param Recorrido $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Recorrido $entity)
    {
        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        $form = $this->createForm(new RecorridoType($securityContext), $entity, array(
            'action' => $this->generateUrl('recorrido_update', array('id' => $entity->getId(),'sucursal'=>$sucursal)),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Actualizar Planilla'));

        return $form;
    }
    /**
     * Edits an existing Recorrido entity.
     *
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecorridoBundle:Recorrido')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recorrido entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->actualizarRetiros($entity);

            return $this->redirect($this->generateUrl('recorrido_edit', array('id' => $id)));
        }

        return $this->render('RecorridoBundle:Recorrido:new-edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'editing' => true,
        ));
    }



    /**
     * Deletes a Recorrido entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RecorridoBundle:Recorrido')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Recorrido entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('recorrido'));
    }

    /**
     * Deletes a Retiro link to the Recorrido entity.
     *
     */
    public function retiroDeleteAction(Request $request, $id_recorrido_retiro, $id_recorrido)
    {
        $em = $this->getDoctrine()->getManager();

        $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($id_recorrido);

        $recorrido_retiro = $em->getRepository('RecorridoBundle:RecorridoRetiro')->find($id_recorrido_retiro);

        if (!$recorrido || !$recorrido_retiro) {
            throw $this->createNotFoundException('Unable to find Recorrido or Retiro entity.');
        }

        $idRetiro = $recorrido_retiro->getIdRetiro();
        $myLimit = 2;
        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($idRetiro);
        $estado = null;
        $distribuidor = null;
        if($recorrido->getEsEntrega()) {
            $estadoN = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo' => 'EPR'));
            $estado = $estadoN;
            $distribuidor = null;
            $nroPlanilla = 0;
            $fechaPlanilla = null;
            $detalle = "ELIMINACION DE PLANILLA";
            $this->sacarPiezaDePlanilla($retiro, $estado, $distribuidor,$nroPlanilla,$fechaPlanilla,$detalle);
            //20-01-2017 PICCINI COMENTO PORQUE PIDEN QUE CUANDO SE ELIMINE UNA PIEZA DE UNA PLANILLA QUEDE EN EPR
            /*$cambios = $em->getRepository('TrackerBundle:Tracker')->findBy(
                array('retiro' => $retiro),
                array('timestampModificacion' => 'DESC'),
                $myLimit);

            $ultimoEstado = $cambios[0]->getEstado();
            foreach($cambios as $cambio) {
                if($cambio->getId() != $ultimoEstado->getId()) {
                    $estado = $cambios[0]->getEstado();
                    $distribuidor = $cambios[0]->getDistribuidor();
                    $nroPlanilla = $cambios[0]->getNroPlanilla();
                    $fechaPlanilla = $cambios[0]->getFechaPlanilla();
                }else{
                }
            }*/
        } else {
            $cambios = $em->getRepository('TrackerBundle:Tracker')->findBy(
                array('retiro' => $retiro),
                array('timestampModificacion' => 'DESC'));
            $ultimoEstado = $cambios[0]->getEstado();
            foreach($cambios as $cambio) {
                if($cambio->getId() != $ultimoEstado->getId()) {
                    $estado = $cambio->getEstado();
                    $distribuidor = $cambios[0]->getDistribuidor();
                    $nroPlanilla = $cambios[0]->getNroPlanilla();
                    $fechaPlanilla = $cambios[0]->getFechaPlanilla();
                }
            }
            $retiro->getDatosEnvios()->setDebeRetirarse(true);
            $retiro->getDatosEnvios()->setConfirmada(true);

            $this->cambiarEstadoRetiro2($retiro, $estado, $distribuidor,$nroPlanilla,$fechaPlanilla);

        }

        $em2 = $this->getDoctrine()->getManager();
        $em2->persist($retiro);
        $em2->flush();

        $recorrido->removeRecorridosRetiro($recorrido_retiro);
        $em->remove($recorrido_retiro);
        $em->persist($recorrido);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($recorrido_retiro, "json");

        return new Response('{"data":'.$json."}");
    }

    /**
     * Devuelve un arreglo con los errores.
     * Si el arreglo esta vacio no hay errores
     *
     * @param \Presis\RecorridoBundle\Entity\Recorrido $recorrido
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return array
     */
    private function checkEstadoDeRetiro(\Presis\RecorridoBundle\Entity\Recorrido $recorrido, \Presis\RetiroBundle\Entity\Retiro $retiro)
    {

        $errores = array();

        $id_retiro = trim($retiro->getId());

        if (!$retiro->isSeleccionableParaRecorrido()) {
            $estado = $retiro->getEstado()->getNombre();
            $errores[] = "El estado del retiro  $id_retiro es $estado. No puede ser agregado a esta planilla";
        }
        if ($recorrido->getRetiros()->contains($retiro)) {
            $errores[] = "El retiro $id_retiro ya fue agregado";
        }
        return $errores;
    }

    /**
     * Adds a Retiro to the Recorrido entity.
     *
     */
    public function addRetiroAction(Request $request, $id_retiro, $id_recorrido, $id_sucursal)
    {

        $empresa = $this->container->getParameter('empresa');

        $em = $this->getDoctrine()->getManager();

        $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($id_recorrido);

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);

        $sucursal = $em->getRepository('PresisGeneralBundle:Sucursal')->findOneBy(array('id'=>$id_sucursal));


        //29-03-17 PEDIDO POR MASLOGISTICA (ROBERTO) SACAR VALIDACION HASTA NUEVO AVISO
        /*if($empresa=='maslogistica'){
            if($retiro->getCobrado()==false){
                return new Response("El retiro $id_retiro aun no fue abonado.", Response::HTTP_CONFLICT);
            }
        }*/

        if(!$retiro){
            return new Response("El retiro $id_retiro no existe", Response::HTTP_CONFLICT);
        }

        if($retiro->getDatosEnvios()->getConfirmada()!= TRUE){
            return new Response("El retiro $id_retiro no existe", Response::HTTP_CONFLICT);
        }

        if (!$recorrido || !$retiro) {
            return new Response("El retiro $id_retiro no existe", Response::HTTP_CONFLICT);
        }
        $errores = $this->checkEstadoDeRetiro($recorrido, $retiro);
        if($errores) {
            return new Response(implode(". ", $errores), Response::HTTP_CONFLICT);
        }

        $recorrido_retiro = $em->getRepository('RecorridoBundle:RecorridoRetiro')->findOneBy(array('retiro' => $retiro, 'fechaCreacion' => new \DateTime()));

        if ($recorrido_retiro) {
            $distribuidor = $recorrido_retiro->getDistribuidor();
            if(!$distribuidor){
                $recorrido->removeRecorridosRetiro($recorrido_retiro);
                $em->remove($recorrido_retiro);
                $em->flush();
                /*$em->persist($recorrido);
                $em->flush();*/
            }else{
                $recorrido->removeRecorridosRetiro($recorrido_retiro);
                $em->remove($recorrido_retiro);
                $em->flush();
                //return new Response("La pieza esta preasignada al distribuidor $distribuidor, con fecha de hoy.", Response::HTTP_CONFLICT);
            }
        }

        $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo' => 'RC'));

        $this->cambiarEstadoRetiro2($retiro, $estado, $recorrido->getDistribuidor(), $id_recorrido, $recorrido->getFecha(),$sucursal);
        //PICCINI - AGREGO PARA QUE AGREGUE LA FECHA DE ULTIMO ESTADO AL INGRESAR UNA PIEZA A PLANILLA
        $retiro->setFechaUltimoEstado(new \DateTime());
        //********************************************************************************************

        $recorrido->addRetiro($retiro);
        $em->persist($recorrido);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($retiro, "json");

        return new Response('{"data":'.$json."}");
    }

    public function addRetirosAction(Request $request)
    {
        $vars = $request->query->all();
        $em = $this->getDoctrine()->getManager();

        $distribuidor = $em->getRepository("DistribuidorBundle:Distribuidor")->find($vars['distribuidor']);
        $fecha = ($vars['fecha'] == "") ? new \DateTime() : \DateTime::createFromFormat('d/m/Y', $vars['fecha']);

        $recorrido = $em->getRepository("RecorridoBundle:Recorrido")->findOneBy(
            array(
                'distribuidor' => $distribuidor,
                'fecha' => $fecha,
                'esEntrega' => false));
        if(!$recorrido) {
            $recorrido = new Recorrido();
            $recorrido->setDistribuidor($distribuidor);
            $recorrido->setFecha($fecha);
            $recorrido->setEsEntrega(false);

            $em->persist($recorrido);
            $em->flush();
        }

        $retirosNoInsertados = "";
        foreach($vars['ids'] as $id_retiro) {
            $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);

            if (!$retiro) {
                $retirosNoInsertados .= "El retiro $id_retiro no existe<br>";
            }

            $errores = $this->checkEstadoDeRetiro($recorrido, $retiro);
            if(!$errores) {
                $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo' => 'ST'));
                $this->cambiarEstadoRetiro2($retiro, $estado, $recorrido->getDistribuidor(), $recorrido->getId(), $recorrido->getFecha());

                $retiro->getDatosEnvios()->setDebeRetirarse(false);
                $retiro->setFechaHoraEntrega(new \DateTime());
                $recorrido->addRetiro($retiro);
            } else {
                $retirosNoInsertados .= implode(". ", $errores) . "<br>";
            }
        }
        $em->persist($recorrido);
        $em->flush();

        return $this->redirect($this->generateUrl('recorrido_edit', array(
            'id' => $recorrido->getId(),
            'retirosNoInsertados' => $retirosNoInsertados)));
    }

    /**
     * Devuelve los retiros que tienen la clave remito, que se puede repetir
     *
     */
    public function getRetirosRemitoAction(Request $request, $id_retiro_atributo, $id_recorrido)
    {
        $em = $this->getDoctrine()->getManager();
        $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($id_recorrido);

        $retiros = $em->getRepository('PresisRetiroBundle:Retiro')->findBy(
            array('remito' => $id_retiro_atributo),
            array('fechHora' => "DESC"));

        $retirosValidos = null;
        if($retiros) {
            $retirosValidos = array();
            foreach ($retiros as $retiro) {
                $errores = $this->checkEstadoDeRetiro($recorrido, $retiro);
                if (!$errores) {
                    $retirosValidos[] = $retiro;
                }
            }
        }

        if (!$recorrido || !$retirosValidos) {
            return new Response("No hay disponible un retiro con número de remito $id_retiro_atributo", Response::HTTP_CONFLICT);
        }

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($retirosValidos, "json"));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Devuelve los retiros que tienen la clave guia agente, que se puede repetir
     *
     */
    public function getRetirosGuiaAgenteAction(Request $request, $id_retiro_atributo, $id_recorrido)
    {
        $em = $this->getDoctrine()->getManager();
        $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($id_recorrido);

        $datosEnvios = $em->getRepository('DatosEnviosBundle:DatosEnvios')->findBy(
            array('guiaAgente' => $id_retiro_atributo),
            array('fecha' => "DESC"));

        $retirosValidos = null;
        if($datosEnvios) {
            $retirosValidos = array();
            foreach ($datosEnvios as $de) {
                $r = $de->getRetiro();
                $errores = $this->checkEstadoDeRetiro($recorrido, $r);
                if (!$errores) {
                    $retirosValidos[] = $r;
                }
            }
        }

        if (!$recorrido || !$retirosValidos) {
            return new Response("No hay disponible un retiro con número de Guia Agente $id_retiro_atributo", Response::HTTP_CONFLICT);
        }

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($retirosValidos, "json"));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Inserta el retiro sin planilla asociada, cuando se importa un csv de la forma
     *
     *                   id_retiro;cod_distribuidor
     *
     */
    public function planillarRetiroAction(Request $request, $id_retiro, $cod_distribuidor)
    {
        $em = $this->getDoctrine()->getManager();

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);

        if($retiro->getDatosEnvios()->getConfirmada()!= TRUE){
            return new Response("El retiro $id_retiro no esta confirmado", Response::HTTP_CONFLICT);
        }

        if (!$retiro) {
            return new Response("El retiro $id_retiro no existe", Response::HTTP_CONFLICT);
        }
        if (!$retiro->isSeleccionableParaRecorrido()) {
            $estado = $retiro->getEstado()->getNombre();
            return new Response("No se puede agregar el par retiro $id_retiro, distribuidor $cod_distribuidor. El estado del retiro es $estado", Response::HTTP_CONFLICT);
        }
        $distribuidor = $em->getRepository('DistribuidorBundle:Distribuidor')->findOneBy(array('codigo' => $cod_distribuidor));
        if (!$distribuidor) {
            return new Response("El distribuidor con código $cod_distribuidor no existe", Response::HTTP_CONFLICT);
        }
        $recorrido_retiro = $em->getRepository('RecorridoBundle:RecorridoRetiro')->findOneBy(array('retiro' => $retiro, 'distribuidor' => $distribuidor->getId(), 'recorrido' => null));
        if ($recorrido_retiro) {
            return new Response("El par retiro $id_retiro, distribuidor $cod_distribuidor ya fue agregado", Response::HTTP_CONFLICT);
        }
        $recorrido_retiro = $em->getRepository('RecorridoBundle:RecorridoRetiro')->findOneBy(array('retiro' => $retiro, 'distribuidor' => $distribuidor->getId(), 'fechaCreacion' => new \DateTime()));
        if ($recorrido_retiro) {
            return new Response("Ya existe el retiro $id_retiro, distribuidor $cod_distribuidor con fecha de hoy", Response::HTTP_CONFLICT);
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $recorrido_retiro = new RecorridoRetiro(null, $retiro);
        $recorrido_retiro->setDistribuidor($distribuidor);
        $recorrido_retiro->setUser($user);

        $em->persist($recorrido_retiro);
        $em->flush();

        $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo' => 'EPR'));

        $this->cambiarEstadoRetiro3($recorrido_retiro->getRetiro(), $estado, $distribuidor);

        return new Response("El par retiro $id_retiro, distribuidor $cod_distribuidor fue agregado exitosamente");
    }

    //AGREGO OTRA FUNCION PARA PROBAR EL SETEO DEL DISTRIBUIDOR
    private function cambiarEstadoRetiro2(\Presis\RetiroBundle\Entity\Retiro $retiro,
                                          \Presis\EstadoBundle\Entity\Estado $estado = null,
                                          \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor = null,$nroPlanilla,$fechaPlanilla,$id_sucursal)
    {

        $em = $this->getDoctrine()->getManager();

        $retiro->setEstado($estado);
        $retiro->setNroPlanilla($nroPlanilla);
        $retiro->setFechaPlanilla($fechaPlanilla);
        $retiro->setDistribuidor($distribuidor);
        $retiro->setFechaUltimoEstado($fechaPlanilla);
        $retiro->setFechaHoraEntrega(new \DateTime());
        $retiro->setSucursal($id_sucursal);
        /* También actualiza la entidad Tracker */
        $tracker = new Tracker();
        $tracker->setRetiro($retiro);
        $tracker->setEstado($estado);
        $tracker->setDistribuidor($distribuidor);
        $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
        $tracker->setNroPlanilla($nroPlanilla);
        $tracker->setFechaPlanilla($fechaPlanilla);
        $tracker->setSucursal($id_sucursal);
        $tracker->setReceptorFechaHora(new \DateTime());
        $retiro->addHistorico($tracker);

        $em->persist($retiro);
        $em->flush();
    }

    private function cambiarEstadoRetiro(\Presis\RetiroBundle\Entity\Retiro $retiro, \Presis\EstadoBundle\Entity\Estado $estado,
                                         $id_recorrido = 0, \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor)
    {

        $em = $this->getDoctrine()->getManager();

        $retiro->setEstado($estado);

        /* También actualiza la entidad Tracker */
        $tracker = new Tracker();
        $tracker->setRetiro($retiro);
        $tracker->setEstado($estado);
        $tracker->setDistribuidor($distribuidor);
        $tracker->setFechaPlanilla($retiro->getFechaPlanilla());
        $tracker->setReceptorFechaHora($retiro->getFechaPlanilla());
        $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
        $tracker->setNroPlanilla($retiro->getNroPlanilla());

        $retiro->addHistorico($tracker);

        $em->persist($retiro);
        $em->flush();
    }
    //PICCINI - BORRO FECHA PLANILLA, NRO PLANILLA Y ACTUALIZAR FECHAULTIMOESTADO, FECHAHORAENTREGA CON NOW()
    //06-01-2017 - PICCINI - BORRO DETALLE ENTREGA
    private function cambiarEstadoRetiro3(\Presis\RetiroBundle\Entity\Retiro $retiro, \Presis\EstadoBundle\Entity\Estado $estado=null,
                                          \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor)
    {

        $em = $this->getDoctrine()->getManager();

        $retiro->setEstado($estado);
        $retiro->setFechaHoraEntrega(new \DateTime());
        $retiro->setFechaUltimoEstado(new \DateTime());
        $retiro->setNroPlanilla(null);
        $retiro->setFechaPlanilla(null);
        $retiro->setDetalleEntrega("");
        /* También actualiza la entidad Tracker */
        $tracker = new Tracker();
        $tracker->setRetiro($retiro);
        $tracker->setEstado($estado);
        $tracker->setDistribuidor($distribuidor);
        $tracker->setReceptorFechaHora(new \DateTime());
        $tracker->setUser($this->container->get('security.context')->getToken()->getUser());

        $retiro->addHistorico($tracker);

        $em->persist($retiro);
        $em->flush();
    }

    //AGREGO OTRA FUNCION PARA PROBAR EL SETEO DEL DISTRIBUIDOR
    private function sacarPiezaDePlanilla(\Presis\RetiroBundle\Entity\Retiro $retiro,
                                          \Presis\EstadoBundle\Entity\Estado $estado = null,
                                          \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor = null,$nroPlanilla,$fechaPlanilla,$detalle)
    {
        $em = $this->getDoctrine()->getManager();

        $retiro->setEstado($estado);
        $retiro->setNroPlanilla($nroPlanilla);
        $retiro->setFechaPlanilla($fechaPlanilla);
        $retiro->setDistribuidor($distribuidor);
        $retiro->setFechaUltimoEstado($fechaPlanilla);
        $retiro->setFechaHoraEntrega(new \DateTime());
        /* También actualiza la entidad Tracker */
        $tracker = new Tracker();
        $tracker->setRetiro($retiro);
        $tracker->setEstado($estado);
        $tracker->setDistribuidor($distribuidor);
        $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
        $tracker->setNroPlanilla($nroPlanilla);
        $tracker->setFechaPlanilla($fechaPlanilla);
        $tracker->setReceptorFechaHora(new \DateTime());
        $tracker->setDetalles($detalle);
        $retiro->addHistorico($tracker);

        $em->persist($retiro);
        $em->flush();
    }

    /**
     * Agrega el recorrido indicado a los RecorridoRetiro adecuados
     *
     */
    public function importarPlanilladosAction(Request $request)
    {
        $id_recorrido = $request->get('id_recorrido');
        $fecha = \DateTime::createFromFormat('d/m/Y', $request->get('fecha'));
        $id_distribuidor = $request->get('id_distribuidor');

        $em = $this->getDoctrine()->getManager();
        $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($id_recorrido);

        $recorrido_retiros = $em->getRepository('RecorridoBundle:RecorridoRetiro')->findBy(
            array('distribuidor' => $id_distribuidor,
                'recorrido' => null,
                'fechaCreacion' => $fecha));

        $orden = 1;
        foreach($recorrido_retiros as $rr) {

            //die("HOLA: ".$rr->getRetiro()->getId());

            $rr->setRecorrido($recorrido);
            $rr->setOrden($orden);

            $retiro = $rr->getRetiro();
            $retiro->setNroPlanilla($recorrido->getId());
            $retiro->setFechaPlanilla($recorrido->getFecha());
            $retiro->setDistribuidor($recorrido->getDistribuidor());
            $retiro->setFechaUltimoEstado(new \DateTime());

            $em->persist($rr);
            $em->flush();

            $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo' => 'RC'));

            $distribuidor = $em->getRepository('DistribuidorBundle:Distribuidor')->findOneBy(array('id' => $id_distribuidor));

            $this->cambiarEstadoRetiro($retiro, $estado, $recorrido->getId(), $distribuidor);
            $em->persist($retiro);
            $em->flush();

            $orden++;
        }

        return new Response(($orden-1)." retiros planillados");
    }

    /**
     * Cierra la planilla de recorrido
     *
     */
    public function cerrarPlanillaAction($id_recorrido)
    {
        $em = $this->getDoctrine()->getManager();
        $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($id_recorrido);

        if (!$recorrido) {
            throw $this->createNotFoundException('Unable to find Recorrido entity.');
        }

        $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('nombre' => 'Distribución'));

        $recorrido->setCerrada(true);

        /* TODO: Revisar si es necesario cambiar estado de los retiros a Distribuída.
         * $recorrido->finalizarRetiros($estado, $this->container->get('security.context')->getToken()->getUser());
         */
        $em->persist($recorrido);
        $em->flush();

        return new Response();
    }

    /**
     * Creates a form to delete a Recorrido entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recorrido_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Abre otra planilla solo si existe
     *
     */
    public function cargarPlanillaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($id);

        if ($recorrido) {
            return new Response("La planilla existe.");
        }

        return new Response("La planilla no existe", Response::HTTP_CONFLICT);
    }

    /**
     * @param $recorrido
     */
    private function actualizarRetiros($recorrido)
    {
        $em = $this->getDoctrine()->getManager();
        $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo' => 'RC'));

        $retiros = $recorrido->getRetiros();
        foreach ($retiros as $retiro) {
            $this->cambiarEstadoRetiro2($retiro, $estado, $recorrido->getDistribuidor(), $recorrido->getId(), $recorrido->getFecha());
        }
    }
}
