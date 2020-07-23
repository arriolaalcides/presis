<?php

namespace Presis\TrackerBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\TrackerBundle\Entity\Tracker;
use Symfony\Component\HttpFoundation\Response;
use Presis\EstadoBundle\Entity\Estado;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\RetiroBundle\Entity\Motivo;
use Presis\UserBundle\Entity\User;
use Presis\TrackerBundle\Form\TrackerType;
use Symfony\Component\HttpFoundation\Request;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Tracker controller.
 *
 */
class TrackerController extends Controller
{
    /**
     * Returns the tracker history for one Retiro
     *
     */
    public function asajaxAction()
    {

        $trackingNo = $this->get('request')->request->get('tracking');

        $json = '[]';
        if(trim($trackingNo) != ''){

            $em = $this->getDoctrine()->getManager();

            $securityContext = $this->container->get('security.context');

            $user=$securityContext->getToken()->getUser();

            $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('id' => $trackingNo));

            if(trim($retiro->getCreateguia())!=trim($user)){
                if($user->hasRole('ROLE_SUCURSAL')){
                    if($retiro && $this->sucursalPermitida($retiro)){
                        $historicos = $retiro->getHistoricos();
                        $serializer = $this->get('jms_serializer');
                        $json = $serializer->serialize($historicos, "json");
                    }
                }else if($user->hasRole('ROLE_DISTRIBUIDOR')){
                    if($retiro && $this->distribuidorPermitido($retiro)) {
                        $historicos = $retiro->getHistoricos();
                        $serializer = $this->get('jms_serializer');
                        $json = $serializer->serialize($historicos, "json");
                    }
                }else{
                    $historicos = $retiro->getHistoricos();
                    $serializer = $this->get('jms_serializer');
                    $json = $serializer->serialize($historicos, "json");
                }

            }else{
                $historicos = $retiro->getHistoricos();
                $serializer = $this->get('jms_serializer');
                $json = $serializer->serialize($historicos, "json");
            }
        }
        return new Response('{"data":'.$json."}");
    }
    
    public function newMovistarAction()
    {
        $entity = new Tracker();
        $form   = $this->createCreateForm($entity);

        return $this->render('TrackerBundle:Tracker:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
 
    }
    
    private function createCreateForm(Tracker $entity)
    {
        $securityContext = $this->container->get('security.context');
        $form = $this->createForm(new TrackerType($securityContext), $entity, array(
            'action' => $this->generateUrl('tracker_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Tracking','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    
    public function createAction(Request $request)
    {
        $entity = new Tracker();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tracker_show', array('id' => $entity->getId())));
        }

        return $this->render('TrackerBundle:Tracker:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Adds a tracking history item
     *
     */
    public function newTrackingAction()
    {

        $posted_values = $this->get('request')->request->all();
        $em = $this->getDoctrine()->getManager();

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($posted_values["tracking"]);

        if($retiro->getDatosEnvios()->getConfirmada()==false){
            return new Response("El retiro ".$retiro->getId()." no esta confirmado", Response::HTTP_CONFLICT);
        }

        /*$twodaysfromnow = $retiro->getDatosEnvios()->getFechaPactada();
        $twodaysfromnow->modify('+2 days');

        die($twodaysfromnow->format('Y-m-d'));*/

        $json = "";
        if ($retiro) {

            $entity = new Tracker();

            $entity->setUser($this->container->get('security.context')->getToken()->getUser());
            $entity->setRetiro($retiro);

            $entity->setNroPlanilla($retiro->getNroPlanilla());

            $estado = $em->getRepository('EstadoBundle:Estado')->find($posted_values["presis_trackerbundle_tracker"]["estado"]);


            if($estado) {
                $entity->setEstado($estado);
            } else {
                $entity->setEstado($retiro->getEstado());

            }

            //SACAMOS LOS MOTIVOS PORQUE NO SE USAN
            //$motivo = $em->getRepository('PresisRetiroBundle:Motivo')->find($posted_values["presis_trackerbundle_tracker"]["motivo"]);
            //$entity->setMotivo($motivo);

            $entity->setReceptorNombre($posted_values["presis_trackerbundle_tracker"]["receptorNombre"]);

            $entity->setReceptorApellido($posted_values["presis_trackerbundle_tracker"]["receptorApellido"]);

            $entity->setDetalles($posted_values["presis_trackerbundle_tracker"]["detalles"]);

            $entity->setDni($posted_values["presis_trackerbundle_tracker"]["dni"]);

            $distribuidor = $em->getRepository('DistribuidorBundle:Distribuidor')->find($posted_values["presis_trackerbundle_tracker"]["distribuidor"]);

            $ultimoTracker = $em->getRepository('TrackerBundle:Tracker')->findOneBy(
                array('retiro' => $retiro),
                array('timestampModificacion' => 'DESC'));

            if($distribuidor) {
                $entity->setDistribuidor($distribuidor);
                $entity->setFechaPlanilla($ultimoTracker->getFechaPlanilla());
            } else {
                if($ultimoTracker) {
                    $distribuidor = $ultimoTracker->getDistribuidor();
                    $entity->setNroPlanilla($ultimoTracker->getNroPlanilla());
                    $entity->setDistribuidor($ultimoTracker->getDistribuidor());
                    $entity->setFechaPlanilla($ultimoTracker->getFechaPlanilla());
                }
            }


            $date = $posted_values["presis_trackerbundle_tracker"]["receptorFechaHora"];
            //$date = \DateTime::createFromFormat('d/m/Y H:i', $date);
            $date = \DateTime::createFromFormat('Y-m-d H:i', $date);
            $entity->setReceptorFechaHora($date);


            $em->persist($entity);
            $em->flush();

            /* Los últimos estados, motivos y cartero se guardan también en el retiro */
            if($retiro->getEstado()->getCodigo()=='ST'){
                $retiro->getDatosEnvios()->setDebeRetirarse(1);
            }
            $retiro->setEstado($estado);
            //$retiro->setMotivo($motivo);
            $retiro->setDistribuidor($distribuidor);
            $retiro->setFechaUltimoEstado($date);
            $retiro->setFechaHoraEntrega($date);
            $retiro->setDetalleEntrega($posted_values["presis_trackerbundle_tracker"]["detalles"]);
            $retiro->setReceptorNombre($posted_values["presis_trackerbundle_tracker"]["receptorNombre"]);
            $retiro->setReceptorApellido($posted_values["presis_trackerbundle_tracker"]["receptorApellido"]);
            $retiro->setDni($posted_values["presis_trackerbundle_tracker"]["dni"]);

            //07-01-17 PICCINI CALCULA LA NUEVA FECHA PACTADA SEGUN ESTADO ELEGIDO
            if($retiro->getDatosEnvios()->getFechaPactada()){

                $delay = $estado->getDelay();
                $newDate = $retiro->getDatosEnvios()->getFechaPactada();
                $newDate->modify('+'.$delay.' days');
                $data = $newDate->format('Y-m-d');
                $date = \DateTime::createFromFormat('Y-m-d', $data);

                $dias = $this->esFinde($date);
                $date->add(new \DateInterval('P'.$dias.'D'));

                while($this->esFeriado($date)==1){
                    $feriado = 1;
                    $date->add(new \DateInterval('P'.$feriado.'D'));

                    $dias = $this->esFinde($date);
                    $date->add(new \DateInterval('P'.$dias.'D'));
                }
                //die("Fecha 3: ".$date->format('Y-m-d'));

                $retiro->getDatosEnvios()->setFechaPactada($date);
            }

            $retiro->getGestionCel()->setEstado($estado);

            $em->persist($retiro);
            $em->flush();
            $serializer = $this->get('jms_serializer');
            $json = $serializer->serialize($entity, "json");
        }
        return new Response('{"data":['.$json."]}");
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

    /**
     * Lists all Tracker entities.
     *
     */
    public function indexAction()
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $entity = new Tracker();

        $distribuidor = ($user->hasRole('ROLE_DISTRIBUIDOR'))? $user->getDistribuidor() : null;

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        $form = $this->createForm(new TrackerType($securityContext), $entity, array(
            'method' => 'POST',
        ));

        return $this->render('TrackerBundle:Tracker:index.html.twig', array(
            'entity' => $entity,
            'distribuidor' => $distribuidor,
            'sucursal' => $sucursal,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Lists all Tracker movistar entities.
     *
     */
    public function indexMovistarAction()
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $entity = new Tracker();

        //$distribuidor = ($user->hasRole('ROLE_DISTRIBUIDOR'))? $user->getDistribuidor() : null;

        //$sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        $form = $this->createForm(new TrackerType($securityContext), $entity, array(
            'method' => 'POST',
        ));

        return $this->render('TrackerBundle:Tracker:index-movistar.html.twig', array(
            'entity' => $entity,
            /*'distribuidor' => $distribuidor,
            'sucursal' => $sucursal,*/
            'form'   => $form->createView(),
        ));
    }


    /**
     * Finds and displays a Tracker entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrackerBundle:Tracker')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tracker entity.');
        }

        return $this->render('TrackerBundle:Tracker:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Deletes a tracking record.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrackerBundle:Tracker')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tracker entity.');
        }

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($entity->getRetiro());

        //PICCINI - SE ANULA PORQUE LUEGO DE UN PDO GUARDAN LOS DATOS DE DEVOLUCION AL CLIENTE.
        //if($entity->getEstado()->getId()=='39'){
        //}

        /*$retiro->setDetalleEntrega('');
        $retiro->setFechaHoraEntrega(NULL);
        $retiro->setReceptorNombre('');
        $retiro->setReceptorApellido('');
        $retiro->setDni('');*/

        $em->remove($entity);
        $em->flush();

        $ultimoEstado = $em->getRepository('TrackerBundle:Tracker')->findOneBy(
            array('retiro' => $entity->getRetiro()),
            array('id' => "DESC"));


        $retiro->setEstado($ultimoEstado->getEstado());
        $retiro->setNroPlanilla($ultimoEstado->getNroPlanilla());
        //CUANDO UN ADMIN BORRA UN ESTADO DEL TRACKER VUELVE PARA ATRAS EN GESTION CEL Y EN RETIRO (AL ESTADO ANTERIOR)
        $retiro->getGestionCel()->setEstado($ultimoEstado->getEstado());


        if($ultimoEstado->getEstado()=='SC - Solicitud Confirmada'){
            $retiro->getDatosEnvios()->setDebeRetirarse(1);
        }

        $retiro->setFechaHoraEntrega($ultimoEstado->getReceptorFechaHora());

        $retiro->setDistribuidor($ultimoEstado->getNombreDistribuidor());
        $retiro->setFechaPlanilla($ultimoEstado->getFechaPlani());
        $retiro->setDetalleEntrega($ultimoEstado->getDetalles());
        $retiro->setFechaUltimoEstado($ultimoEstado->getReceptorFechaHora());
        $retiro->setReceptorNombre($ultimoEstado->getReceptorNombre());
        $retiro->setReceptorApellido($ultimoEstado->getReceptorApellido());
        $retiro->setDni($ultimoEstado->getDni());

        $em->persist($retiro);
        $em->flush();

        return new Response("Tracker record $id deleted succesfully", Response::HTTP_OK);
    }

    /**
     * Devuelve los retiros que tienen la clave remito, que se puede repetir
     *
     */
    public function getRetirosRemitoAction(Request $request, $id_retiro_atributo)
    {
        $em = $this->getDoctrine()->getManager();

        $retiros = $em->getRepository('PresisRetiroBundle:Retiro')->findBy(
            array('remito' => $id_retiro_atributo),
            array('fechHora' => "DESC"));

        if (!$retiros) {
            return new Response("No hay disponible un retiro con número de remito $id_retiro_atributo", Response::HTTP_CONFLICT);
        }

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($retiros, "json"));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Devuelve los retiros que tienen la clave guia agente, que se puede repetir
     *
     */
    public function getRetirosGuiaAgenteAction(Request $request, $id_retiro_atributo)
    {
        $em = $this->getDoctrine()->getManager();

        $datosEnvios = $em->getRepository('DatosEnviosBundle:DatosEnvios')->findBy(
            array('guiaAgente' => $id_retiro_atributo),
            array('fecha' => "DESC"));

        if (!$datosEnvios) {
            return new Response("No hay disponible un retiro con número de Guia Agente $id_retiro_atributo", Response::HTTP_CONFLICT);
        }

        $retiros = null;
        if($datosEnvios) {
            $retiros = array();
            foreach ($datosEnvios as $de) {
                $retiros[] = $de->getRetiro();
            }
        }

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($retiros, "json"));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Devuelve los retiros que tienen la clave guia agente, que se puede repetir
     *
     */
    public function getRetirosImeiAction(Request $request, $id_retiro_atributo)
    {
        $em = $this->getDoctrine()->getManager();

        $datosEnvios = $em->getRepository('GestionCelBundle:GestionCel')->findBy(
            array('nroserie' => $id_retiro_atributo),
            array('fechaBase' => "DESC"));

        if (!$datosEnvios) {
            return new Response("No hay disponible una gestion con número de IMEI $id_retiro_atributo", Response::HTTP_CONFLICT);
        }

        $retiros = null;
        if($datosEnvios) {
            $retiros = array();
            foreach ($datosEnvios as $de) {
                $retiros[] = $de->getRetiro();
            }
        }

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($retiros, "json"));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Cambia el estado del retiro indicado, cuando se importa un csv
     *
     * Solo id_retiro;cod_estado son obligatorios
     *
     */
    public function cambiarEstadoAction(Request $request)
    {
        $post = $request->request->all();

        //die("HOLA: ".$post['retiro']);

        $em = $this->getDoctrine()->getManager();

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($post['retiro']);
        if (!$retiro) {
            return new Response("El retiro ".$post['retiro']." no existe", Response::HTTP_CONFLICT);
        }

        if($retiro->getDatosEnvios()->getConfirmada()==false){
            return new Response("El retiro ".$post['retiro']." no esta confirmado", Response::HTTP_CONFLICT);
        }

        $estado = ($post['estado'])?$em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo' => $post['estado'])):null;
        if (!$estado) {
            return new Response("El estado ".$post['estado']." no existe", Response::HTTP_CONFLICT);
        }
        $post['estado'] = $estado->getId();

        $motivo = (isset($post['motivo']))?$em->getRepository('PresisRetiroBundle:Motivo')->findOneBy(array('codigo' => $post['motivo'])):null;
        $post['motivo'] = ($motivo)?$motivo->getId():null;

        $distribuidor = (isset($post['distribuidor']))?$em->getRepository('DistribuidorBundle:Distribuidor')->findOneBy(array('codigo' => $post['distribuidor'])):null;
        $post['distribuidor'] = ($distribuidor)?$distribuidor->getId():null;

        $hydrator = new DoctrineHydrator($em);
        $t = new Tracker();

        $tracker = $hydrator->hydrate($post, $t);

        $tracker->getRetiro()->setEstado($estado);
        //$tracker->getRetiro()->setFechaUltimoEstado(new \DateTime('now'));
        $tracker->getRetiro()->setFechaUltimoEstado($tracker->getReceptorFechaHora());
        $tracker->getRetiro()->setDistribuidor($distribuidor);

        $tracker->getRetiro()->setDetalleEntrega($tracker->getDetalles());
        $tracker->getRetiro()->setFechaHoraEntrega($tracker->getReceptorFechaHora());
        $tracker->getRetiro()->setReceptorNombre($tracker->getReceptorNombre());
        $tracker->getRetiro()->setReceptorApellido($tracker->getReceptorApellido());
        $tracker->getRetiro()->setDni($tracker->getDni());


        $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
        $tracker->setNroPlanilla($tracker->getRetiro()->getNroPlanilla());

        $retiro->addHistorico($tracker);

        $em->persist($retiro);
        $em->flush();

        return new Response("Estado del retiro ".$tracker->getRetiro()." cambiado a ".$tracker->getRetiro()->getEstado());
    }

    private function sucursalPermitida(\Presis\RetiroBundle\Entity\Retiro $retiro){
        $em = $this->getDoctrine()->getManager();
        $result = true;
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $sucursalLogueada = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        if($sucursalLogueada) {
            $nroPlanilla = ($retiro->getNroPlanilla()?$retiro->getNroPlanilla():0);
            $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($nroPlanilla);
            if($recorrido) {
                //die("RECORRIDO: ".$recorrido->getDistribuidor()."--"." LOGUEADO: ".$distribuidorLogueado);
                if ($recorrido->getSucursal() != $sucursalLogueada) {
                    return false;
                }
            }else{
                return false;
            }
        }
        return $result;
    }

    private function distribuidorPermitido(\Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $em = $this->getDoctrine()->getManager();
        $result = true;

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $distribuidorLogueado = ($user->hasRole('ROLE_DISTRIBUIDOR'))? $user->getDistribuidor() : null;

        if($distribuidorLogueado) {
            $nroPlanilla = ($retiro->getNroPlanilla()?$retiro->getNroPlanilla():0);
            $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($nroPlanilla);
            if($recorrido) {
                //die("RECORRIDO: ".$recorrido->getDistribuidor()."--"." LOGUEADO: ".$distribuidorLogueado);
                if ($recorrido->getDistribuidor() != $distribuidorLogueado) {
                    return false;
                }
            }else{
                return false;
            }
        }
        return $result;
    }

    public function gestionarTrackingAction(){

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $entity = new Tracker();

        //$distribuidor = ($user->hasRole('ROLE_DISTRIBUIDOR'))? $user->getDistribuidor() : null;

        //$sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        $form = $this->createForm(new TrackerType($securityContext), $entity, array(
            'method' => 'POST',
        ));

        return $this->render('TrackerBundle:Tracker:index-tracking-movistar.html.twig', array(
            'entity' => $entity,
            /*'distribuidor' => $distribuidor,
            'sucursal' => $sucursal,*/
            'form'   => $form->createView(),
        ));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tracker_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
            ;
    }

    private function createEditForm(Tracker $entity)
    {
        $edit = 0;
        $empresa = $this->container->getParameter('empresa');
        $securityContext = $this->container->get('security.context');
        $em = $this->getDoctrine()->getEntityManager();
        $form = $this->createForm(new TrackerType($securityContext, "editar",$empresa), $entity, array(
            'action' => $this->generateUrl('tracker_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            //'read_only' => true,
        ));

        $form->add('submit', 'submit', array('label' => 'GUARDAR CAMBIOS', 'attr' => array(
            'class' => 'btn btn-danger btn-sm custom-btn'
        )));

        return $form;
    }

    public function editAction(Request $request, $id)
    {

        $empresa = $this->container->getParameter('empresa');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('TrackerBundle:Tracker')->find($id);
        $retiro_id = $entity->getRetiro();

        if (!$entity) {
            return New Response("TRACKER NO ENCONTRADO");
        }

        $editForm = $this->createEditForm($entity);

        $deleteForm = $this->createDeleteForm($entity);

        if($empresa=='maslogistica'){
            if($user->hasRole('ROLE_ADMIN')){
                return $this->render('TrackerBundle:Tracker:edit-movistar.html.twig', array(
                    'id' => $id,
                    'entity'      => $entity,
                    'retiro'      => $retiro_id,
                    'edit_form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }else{
                if($user->getCliente()->getEmpresa()=='MOVISTAR'){
                    return $this->render('TrackerBundle:Tracker:edit-movistar.html.twig', array(
                        'id' => $id,
                        'entity'      => $entity,
                         'retiro'      => $retiro_id,
                        'edit_form'   => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                    ));
                }else{
                    return $this->render('TrackerBundle:Tracker:edit-movistar.html.twig', array(
                        'id' => $id,
                        'entity'      => $entity,
                         'retiro'      => $retiro_id,
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
    
    
    public function TrackercreateGestionAction(Request $request){

        $posted_values = $this->get('request')->request->all();
        $em = $this->getDoctrine()->getManager();
       
        $rf = explode('/', $posted_values['rf']);
        $rf = $rf[2].'-'.$rf[1].'-'.$rf[0];
        
        $entity = new Tracker();
        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('id'=>$posted_values['guia']));
        $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('id'=>$posted_values['estado']));
        
        $entity->setRetiro($retiro);
        $entity->setNroPlanilla($posted_values["nroPlanilla"]);
        $entity->setEstado($estado);
        $entity->setDetalles($posted_values['detalles']);
        $entity->setDni($posted_values['dni']);
        $entity->setTimestampModificacion(new \DateTime());
        $entity->setReceptorFechaHora(new \DateTime($rf));
        $entity->setObs($posted_values['obs']);
        $entity->setUpdateTracker($posted_values['updateTracker']);
        
        
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em->persist($entity);
        $em->flush();
        
        return new Response($posted_values['rf']);
       
    }

    public function TrackerupdateGestionAction(Request $request){

        $posted_values = $this->get('request')->request->all();
        $em = $this->getDoctrine()->getManager();
       
        $rf = explode('/', $posted_values['rf']);
        $rf = $rf[2].'-'.$rf[1].'-'.$rf[0];
        
        
        $entity = $em->getRepository('TrackerBundle:Tracker')->findOneBy(array('id'=>$posted_values['idtracking']));
        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('id'=>$posted_values['guia']));
        $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('id'=>$posted_values['estado']));
        
        $entity->setRetiro($retiro);
        $entity->setNroPlanilla($posted_values["nroPlanilla"]);
        $entity->setEstado($estado);
        $entity->setDetalles($posted_values['detalles']);
        $entity->setDni($posted_values['dni']);
        $entity->setTimestampModificacion(new \DateTime());
        $entity->setReceptorFechaHora(new \DateTime($rf));
        $entity->setObs($posted_values['obs']);
        $entity->setUpdateTracker($posted_values['updateTracker']);
        
        
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em->persist($entity);
        $em->flush();
        
        return new Response($posted_values['idtracking']);
       
    }
}