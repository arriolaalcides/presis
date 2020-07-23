<?php

namespace Presis\ConstanciaRetiroBundle\Controller;

use Presis\ConstanciaRetiroBundle\Entity\RetirosFijos;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ps\PdfBundle\Annotation\Pdf;
use Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiro;
use Presis\EstadoBundle\Entity\Estado;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\GestionCelBundle\Entity\GestionCel;
use Presis\ConstanciaRetiroBundle\Form\ConstanciaRetiroType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * ConstanciaRetiro controller.
 *
 */
class ConstanciaRetiroController extends Controller
{

    private $SABADO = 6;

    public function cancelarAction(){

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $request=$this->container->get('request_stack')->getCurrentRequest();
        $ids=$request->get('ids');

        $em = $this->getDoctrine()->getManager();
        foreach($ids as $id) {
            $entity = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->find($id);

            $entity->setEstado('CANCELADA');
            $entity->setFechaHora(new \DateTime('now'));
            $entity->setAsigno($user);

            $em->flush();
        }
        return new Response("Ok");
    }

    public function retiradaAction(){

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $request=$this->container->get('request_stack')->getCurrentRequest();
        $ids=$request->get('ids');

        $em = $this->getDoctrine()->getManager();
        foreach($ids as $id) {
            $entity = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->find($id);
            $entity->setFechaRetirado(new \DateTime('now'));
            $entity->setEstado('RETIRADA');
            $em->flush();
        }
        return new Response("Ok");
    }

    /**
     * Lists all ConstanciaRetiro entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->findAll();

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Almacena la solicitud de retiro
     *
     */
    /*public function guardarAction(Request $request)
    {
        $fecha = new \DateTime();
        $hora = date("H:i");

        if($hora>'16:00'){
            $fecha->add(new \DateInterval('P1D'));
        }

        $posted_values = $this->get('request')->request->all();

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $empresa = $this->container->getParameter('empresa');

        $em = $this->getDoctrine()->getManager();

        $hayRetiro = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->findBy(array(
            'empresa'=>$empresa,
            'fecha'=>$fecha
        ));

        if($hayRetiro===false){
            return new Response("YA SE SOLICITO UN RETIRO EL DIA DE HOY");
        }else{

            $newHora = date('H:i');
            $solicitud = new ConstanciaRetiro();
            $solicitud->setFecha($fecha);
            $solicitud->setEmpresa($posted_values['empresa']);
            $solicitud->setCalle($posted_values['calle']);
            $solicitud->setAltura($posted_values['altura']);
            $solicitud->setPiso($posted_values['piso']);
            $solicitud->setDpto($posted_values['dpto']);
            $solicitud->setLocalidad($posted_values['localidad']);
            $solicitud->setProvincia($posted_values['provincia']);
            $solicitud->setCp($posted_values['cp']);
            $solicitud->setUsuario($user);
            $solicitud->setHora(\DateTime::createFromFormat('H:i', $newHora));
            $solicitud->setTimestamp(new \DateTime('now'));

            $em->persist($solicitud);
            $em->flush();
        }
        return new Response("Ok");
    }*/

    /**
     * Creates a new ConstanciaRetiro entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity = new ConstanciaRetiro();

        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $fecha = new \DateTime();
            $hora = date("H:i");

            if($entity->getFecha()->format('Y-m-d')==date('Y-m-d')){
                if($hora>'15:00'){
                    $fecha->add(new \DateInterval('P1D'));
                    $fechaRetiro = $this->excluirFinDeSemana($fecha->format('U'),$fecha->format('U'));
                    $entity->setFecha($fechaRetiro);
                }
            }

            $fechaRetiro = $this->excluirFinDeSemana($entity->getFecha()->format('U'),$entity->getFecha()->format('U'));
            $entity->setFecha($fechaRetiro);
            //die("Final: ".$fechaRetiro->format('Y-m-d'));

            $entity->setTimestamp(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Solicitud '.$entity->getId().' ingresada con exito, sera retirada el dia '.$entity->getFecha()->format('d/m/Y').' durante '.$entity->getFranja())
            ;

            return $this->redirect($this->generateUrl('constanciaretiro_new'));
        }

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
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

    /**
     * Creates a form to create a ConstanciaRetiro entity.
     *
     * @param ConstanciaRetiro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConstanciaRetiro $entity)
    {
        $securityContext = $this->container->get('security.context');
        $form = $this->createForm(new ConstanciaRetiroType($securityContext), $entity, array(
            'action' => $this->generateUrl('constanciaretiro_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'AGREGAR RETIRO'));

        return $form;
    }

    /**
     * Displays a form to create a new ConstanciaRetiro entity.
     *
     */
    public function newAction()
    {
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $entity = new ConstanciaRetiro();

        $cliente = ($user->hasRole('ROLE_CLIENTE'))? $user->getCliente() : null;

        $dataSuc = null;

        if($user->hasRole('ROLE_CLIENTE')){
            $em = $this->getDoctrine()->getManager();
            $dataSuc = $em->getRepository('PresisGeneralBundle:Sucursal')->find($user->getSucursal()->getId());
        }

        $form   = $this->createCreateForm($entity);
        $tipoUser = "";
        if($user->isUserAdmin()==false){
            $tipoUser = "admin";
        }
        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:new.html.twig', array(
            'entity' => $entity,
            'cliente' => $cliente,
            'sucursal' => $user->getSucursal(),
            'dataSuc' => $dataSuc,
            'user' => $tipoUser,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Finds and displays a ConstanciaRetiro entity.
     *
     */
    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConstanciaRetiro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ConstanciaRetiro entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConstanciaRetiro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ConstanciaRetiro entity.
    *
    * @param ConstanciaRetiro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ConstanciaRetiro $entity)
    {
        $form = $this->createForm(new ConstanciaRetiroType(), $entity, array(
            'action' => $this->generateUrl('constanciaretiro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ConstanciaRetiro entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConstanciaRetiro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('constanciaretiro_edit', array('id' => $id)));
        }

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    
    public function updateManifiestosAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();
        $entity = $em->getRepository('PresisGeneralBundle:ManifiestoCarga')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Manifiesto entity.');
        }

        $editForm = $this->createEditManifiestosForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            ///////////////////////
            $guias = $em->getRepository('PresisRetiroBundle:Retiro')->findBy(array('nroConstancia' => $id));
                foreach ($guias as $guia){
                    $estado_retiro_gestion_cel = $guia->getGestioncel()->getEstado();
                    $estado_bolsin_a_galander = $em->getRepository('EstadoBundle:Estado')->find('157'); 
                    $estado_bolsin_a_movistar = $em->getRepository('EstadoBundle:Estado')->find('166'); 

                    ///SI LA SUCURSAL QUE CREO EL MANIFIESTO ES TRUE Y EL ESTADO DE LAS GUIAS ESTAN EN 157  -> ANULAR:
                    if ($entity->getSucursal() == $securityContext->getToken()->getUser()->getSucursal() && $estado_bolsin_a_galander == $estado_retiro_gestion_cel ){

                        $entity->setUsuario($this->container->get('security.context')->getToken()->getUser());
                        $entity->setEstado('ANULADO');
                        //SI USUARIO ES BACKOFFICE LAS GUIAS ENTRAN A ESTADO 165
                        $estado = $em->getRepository('EstadoBundle:Estado')->find('165');
                        $estadoAnterior_movistar = $guia->getGestioncel()->setEstado($estado);
                        $em->persist($estadoAnterior_movistar);
                        $em->flush();

                        ///SI LA SUCURSAL QUE CREO EL MANIFIESTO ES TRUE Y EL ESTADO DE LAS GUIAS ESTAN EN 166  -> ANULAR:
                        }elseif($entity->getSucursal() == $securityContext->getToken()->getUser()->getSucursal() && $estado_bolsin_a_movistar == $estado_retiro_gestion_cel ){
                            $entity->setUsuario($this->container->get('security.context')->getToken()->getUser());
                            $entity->setEstado('ANULADO');
                            $estado = $em->getRepository('EstadoBundle:Estado')->find('160');
                            $estadoAnterior_galander = $guia->getGestioncel()->setEstado($estado);
                            $em->persist($estadoAnterior_galander);
                            $em->flush();
                            
                        }
                    
                }

            return $this->redirect($this->generateUrl('constancia_manifiestos', array('id' => $id)));
        }

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:manifiestoscarga_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
      
        ));
    }
    
    public function manifiestoSearchAction(){
        
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
                'codigo'=>array('EDET', 'ETHR', 'IH', 'RCEC', 'ERCEC', 'EDETM')
            ));

        //$entity = new GestionCel();
        $entity = new \Presis\GeneralBundle\Entity\ManifiestoCarga();
        $id_manifiesto = $entity->getId();
        

        $token = md5('matias'.date('Y-m-d H:i:s'));

        $form = $this->createForm(new \Presis\GeneralBundle\Form\SearchManifiestoCargaType(), $entity, array(
            'action' => $this->generateUrl('constancia_search_manifiesto'),
            'method' => 'POST',
        ));

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:search-manifiesto.html.twig', array(
            'entity' => $entity,
            'sucursales' => $sucursales,
            'sucursal' => $user->getSucursal(),
            'estados' => $estados,
            'token' => $token,
            'form'   => $form->createView(),
        ));
     
    }
    
    
    public function manifiestoAjaxSearchAction(Request $request){
        
        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $attrs = $request->request->all();

        $em = $this->getDoctrine()->getManager();
        
       $query = " SELECT 
                    r.id AS guia, r.fecha_hora AS fechaCreacionGuia,  e.nombre AS estadoFinal, fab.descricion, mo.descripcion,
                    t.retiro_id AS tracker,  
                    gc.usuario, gc.ani, gc.nroserie, gc.nomyape, gc.nrosst, gc.aceptacargos, gc.nivelderep, gc.muleto, gc.imeimuleto, 
                    gc.fechaactivacion, gc.fechafabricacion, gc.origendelequipo, gc.sva, gc.falla, gc.rotura, gc.completitud,
                    gc.estadointervencion, gc.certificadoreparador, gc.placaswap, gc.nroimei, gc.tipocliente, gc.tiposervicio,
                    gc.claimassurant, gc.precinto, gc.trayecto, gc.valordeclaradocel, gc.fecha_base, t.estado_id, 
                    t.receptor_fecha_hora, t.timestamp_modificacion, t.nroPlanilla, t.fecha_planilla, gc.sucursal
                    FROM 
                    PresisRetiroBundle:Retiro r,
                    TrackerBundle:Tracker t,
                    GestionCelBundle:GestionCel g,
                    DatosEnviosBundle:DatosEnvios d,
                    PresisServicioBundle:Servicio se,
                    WHERE gc.fecha_base BETWEEN '2018-01-03' and '2018-01-30'   ";
         
        /*if($attrs['nroConstancia']){
            $query->andWhere('r.nroConstancia = :nroConstancia')->setParameter('nroConstancia', $attrs['nroConstancia']);
        }

        if($attrs["estado"]) {
            $query->andWhere("g.estado = :estado")
                ->setParameter('estado', $attrs["estado"]);
            unset($attrs["estado"]);
        }

        if($attrs["codSuc"]) {
            $query->andWhere("r.sucursal = :codSuc")
                ->setParameter('codSuc', trim($attrs["codSuc"]));
            unset($attrs["codSuc"]);
        }
        
       /* if($attrs['trayecto']){
            $query->andWhere('g.trayecto = :trayecto')->setParameter('trayecto', $attrs['trayecto']);
            unset($attrs['trayecto']);
        }*/
        
        /*if($attrs['cerrado'] == 1){
            $query->andWhere('g.estado in (159, 162)');   
            unset($attrs['cerrado']);
        }
        
        if($attrs["fechaDesde"]) {
            $attrs["fechaDesde"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaDesde"]);
            $query->andWhere('r.fechHora >= :desde')
                ->setParameter('desde', $attrs["fechaDesde"]->format('Y-m-d 00:00:00'));
            unset($attrs["fechaDesde"]);
        }
        

        if($attrs["fechaHasta"]) {
            $attrs["fechaHasta"] = \DateTime::createFromFormat('d/m/Y', $attrs["fechaHasta"]);
            $query->andWhere('r.fechHora <= :hasta')
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


        /*foreach ($attrs as $key => $value) {
            if (trim($value) !== '') {
                $query->andWhere("r.$key = :$key")
                    ->setParameter("$key", $value);
            }
        }*/

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result["total"] = count($paginator);
        $result["rows"] = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
        
     
    }
    
    /**
     * Deletes a ConstanciaRetiro entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ConstanciaRetiro entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('constanciaretiro'));
    }

    /**
     * Creates a form to delete a ConstanciaRetiro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('constanciaretiro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function buscarDatosSucursalAction(Request  $request){

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $dataSuc = $em->getRepository('PresisGeneralBundle:Sucursal')->findOneBy(array('id'=>$user->getSucursal()));

        //die($dataSuc->getDescripcion());

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($dataSuc, "json");

        return new Response($json);
    }

    public function constanciasPendientesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('DistribuidorBundle:Distribuidor')->findAll();
        $query = $em->createQuery('SELECT p.id, p.codigo, p.apellido, p.nombre FROM DistribuidorBundle:Distribuidor p 
        WHERE p.zona!=:zona');
        $query->setParameter('zona', 'INTERIOR');
        $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

        $query2 = $em->createQuery('SELECT c.id, c.empresa FROM PresisGeneralBundle:Cliente c');
        $result2 = $query2->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:constancias-pendientes.html.twig', array(
            'entities' => $result,
            'clientes' => $result2
        ));
    }

    public function cargarRetirosFijos(){
        $dias = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
        //die(date('w', strtotime('now')));
        //$fecha = $dias[date('N', strtotime('now'))];
        $fecha = $dias[date('w', strtotime('now'))];

        $fechaHoy = new \DateTime('now');

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository("ConstanciaRetiroBundle:RetirosFijos")->createQueryBuilder('c');
        $query->andWhere('c.dias LIKE :dias');
        $query->andWhere('c.fechaAsignado != :hoy OR c.fechaAsignado IS NULL');
        $query->andWhere('c.is_habilitado = TRUE');
        $query->orderBy('c.id', 'DESC');
        $query->setParameter('dias', '%' . $fecha . '%');
        $query->setParameter('hoy', $fechaHoy->format('Y-m-d'));
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $c = count($paginator);
        foreach ($paginator as $post) {

            $constanciaRetiro = new ConstanciaRetiro();

            $constanciaRetiro->setFecha(new \DateTime('now'));
            $constanciaRetiro->setCalle($post->getCalle());
            $constanciaRetiro->setAltura($post->getAltura());
            $constanciaRetiro->setPiso($post->getPiso());
            $constanciaRetiro->setDpto($post->getDpto());
            $constanciaRetiro->setLocalidad($post->getLocalidad());
            $constanciaRetiro->setProvincia($post->getProvincia());
            $constanciaRetiro->setCp($post->getCp());
            $constanciaRetiro->setContacto($post->getContacto());
            $constanciaRetiro->setTelefono($post->getTelefono());
            $constanciaRetiro->setMail($post->getMail());
            $constanciaRetiro->setSucursal($post->getSucursal());
            $constanciaRetiro->setCliente($post->getCliente());
            $constanciaRetiro->setFranja($post->getFranja());
            $constanciaRetiro->setObservaciones($post->getObservaciones());
            $constanciaRetiro->setIsFijo(TRUE);

            $post->setFechaAsignado(new \DateTime('now'));

            $em->persist($constanciaRetiro);
            $em->flush();
        }
    }


    public function getPendientesAmbaAction(Request $request)
    {

        $this->cargarRetirosFijos();

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository("ConstanciaRetiroBundle:ConstanciaRetiro")->createQueryBuilder('c');

        if(!$user->hasRole('ROLE_CLIENTE')) {

            $attrs = $request->request->all();

            if ($attrs['distrib']) {
                $distribuidor = $em->getRepository("DistribuidorBundle:Distribuidor")->find($attrs['distrib']);
                $dist = $distribuidor->getCodigo() . ' - ' . $distribuidor->getNombreCompleto();
                $query->andWhere("c.distribuidor = :distrib")
                    ->setParameter('distrib', $dist);
                unset($attrs["distrib"]);
            }

            if ($attrs['fecha']) {
                $attrs["fecha"] = \DateTime::createFromFormat('d/m/Y', $attrs["fecha"]);
                $query->andWhere("c.fecha = :fecha")
                    ->setParameter('fecha', $attrs["fecha"]->format('Y-m-d'));
                unset($attrs["fecha"]);
            }
        }

        if($user->hasRole('ROLE_CLIENTE')){
            if($user->isUserAdmin()==FALSE){
                $query->andWhere("c.usuario = :user")
                    ->setParameter('user', trim($user));
            }else{
                $query->andWhere("c.cliente = :cliente")
                    ->setParameter('cliente', $user->getCliente());
            }
        }

        $query->andWhere("c.estado != :estado OR c.estado IS NULL")
            ->setParameter('estado', 'CANCELADA');

        $query->orderBy('c.id', 'DESC');

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }
    

    
    
    public function addRetiroAmbaAction(Request $request){

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $vars = $request->query->all();

        foreach($vars['ids'] as $id_retiro) {

            $estado = $em->getRepository('EstadoBundle:Estado')->findOneBy(array('codigo' => 'ST'));

            $constancia = $em->getRepository("ConstanciaRetiroBundle:ConstanciaRetiro")->findOneBy(array('id'=>$id_retiro));
            $distribuidor = $em->getRepository("DistribuidorBundle:Distribuidor")->find($vars['distribuidor']);

            //AGREGO PORQUE NO SE CUANDO SE PASA A CONFIRMADA UNA GUIA
            $constancia->setConfirmada(true);

            $constancia->setHabilitado(true);
            $constancia->setEstado($estado);
            $constancia->setDistribuidor($distribuidor);
            $constancia->setFechaHora(new \DateTime());
            $constancia->setAsigno($user);

            if($constancia->getRetiro()){

                $retiro = $em->getRepository("PresisRetiroBundle:Retiro")->findOneBy(array('id'=>$constancia->getRetiro()));

                $tracker = new Tracker();
                $tracker->setRetiro($retiro);
                $tracker->setEstado($estado);
                $tracker->setDistribuidor($distribuidor);
                $tracker->setTimestampModificacion(new \DateTime('now'));
                $tracker->setReceptorFechaHora(new \DateTime('now'));
                $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
                $em->persist($tracker);

            }

            $em->persist($constancia);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('constanciaretiro_pendientes'));
    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showconstanciaAction()
    {
        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retiro entity.');
        }

        if($user->hasRole('ROLE_ADMIN')){
            $entity->setImpreso(true);
        }

        $em->persist($entity);
        $em->flush();

        $format = $this->get('request')->get('_format');

        return $this->render(sprintf('ConstanciaRetiroBundle:Default:guia2_simple.%s.twig', $format), array(
            'retiro' => $entity,
            'attr' => array('target' => '_blank'),
        ));
    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showconstanciasretiroAction()
    {

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $ids = $request->get('ids');

        $cont = 0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal = array();

        $arra = new ArrayCollection();

        foreach ($ids as $id) {

            $cont++;
            if ($cont == 1) {
                $arrpare = array();
            }

            $entity = $em->getRepository('ConstanciaRetiroBundle:ConstanciaRetiro')->find($id);

            $arra->add($entity);

            /*if($user->hasRole('ROLE_ADMIN')){
                $entity->setImpreso(true);
            }*/

            $em->persist($entity);
            array_push($arrpare, $entity);
            if ($cont == 2) {
                array_push($arrtotal, $arrpare);
                $cont = 0;
            }

        }
        if ($cont == 1) {
            array_push($arrtotal, $arrpare);
        }
        $em->flush();

        $format = $this->get('request')->get('_format');
        //  $format="pdf";
        return $this->render(sprintf('ConstanciaRetiroBundle:Default:guia2.%s.twig', $format), array(
            'guias' => $arrtotal,
            'attr' => array('target' => '_blank'),
        ));
    }

    public function getManifiestosAction(){
        $empresa = $this->container->getParameter('empresa');

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        //die("HOLA".$user->getSucursal());

        if($user->hasRole('ROLE_BACK_OFFICE ')){
            $sucursales = $em->getRepository('PresisGeneralBundle:Sucursal')->findBy(array('cliente'=>'13','descripcion'=>trim($user->getSucursal())));
        }else{
            $sucursales = $em->getRepository('PresisGeneralBundle:Sucursal')->findBy(array('cliente'=>'13'));
        }

        $estados = $em->getRepository('EstadoBundle:Estado')->findBy(
            array(
                'codigo'=>array('EDET','ETHR','IH','RF','RCEC','ERCEC','TOCEC','EEC','NRCEC')
            ));

        

        $token = md5('matias'.date('Y-m-d H:i:s'));

        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:listar.html.twig', array(
            'sucursales' => $sucursales,
            'sucursal' => $user->getSucursal(),
            'estados' => $estados,
            'token' => $token,
        ));
    }

    public function listarAjaxAction(Request $request){

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $attrs = $request->request->all();
        $em = $this->getDoctrine()->getManager();

        //$query = $em->getRepository("PresisGeneralBundle:ManifiestoCarga")->createQueryBuilder('m');

        $query = $em->getRepository("PresisGeneralBundle:ManifiestoCarga")->createQueryBuilder('m');
        //$query->leftJoin("FirmasBundle:FirmasManifiesto", "f");
        //$query->where('m.id = trim(f.nroManifiesto)');

        /*if($user->hasRole('ROLE_CLIENTE')){
            $query->andWhere("m.usuario = :user")
                ->setParameter('user', trim($user));
        }*/

        if($attrs['manifiesto']){
            $query->andWhere("m.id = :manifiesto")
                    ->setParameter('manifiesto', trim($attrs['manifiesto']));
             unset($attrs["manifiesto"]);
            }
        
        if($attrs["codSuc"]) {
            $query->andWhere("m.sucursal = :codSuc")
                ->setParameter('codSuc', trim($attrs["codSuc"]));
            unset($attrs["codSuc"]);
        }
        
        if($user->getCliente()->getEmpresa()=="MOVISTAR"){
            if($user->hasRole('ROLE_BACK_OFFICE')){
                $query->andWhere("m.sucursal = :sucursal")
                    ->setParameter('sucursal', trim($user->getSucursal()->getCodSuc()));
            }
        }
        
        if ($attrs["limite"] > 0) {
            $query->setFirstResult($attrs["pagina"] * $attrs["limite"])
                ->setMaxResults($attrs["limite"]);
        }
        unset($attrs["pagina"]);
        unset($attrs["limite"]);


        $query->orderBy('m.id', 'DESC');


        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result["total"] = count($paginator);
        $result["rows"] = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }
    
    
    
    
    //deivid
    public function editarManifiestosAction($id){
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:ManifiestoCarga')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Manifiesto entity.');
        }

        $editForm = $this->createEditManifiestosForm($entity);
        
        return $this->render('ConstanciaRetiroBundle:ConstanciaRetiro:manifiestoscarga_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    private function createEditManifiestosForm(\Presis\GeneralBundle\Entity\ManifiestoCarga $entity)
    {
        $form = $this->createForm(new \Presis\GeneralBundle\Form\ManifiestoCargaType(), $entity, array(
            'action' => $this->generateUrl('manifiestocarga_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    

    public function listarRetirosAjaxAction(Request  $request){

        $nroConstancia = $request->query->get('id');

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $estado = $em->getRepository('EstadoBundle:Estado')->find('157');

        $query = $em->createQueryBuilder();

        $query = $em->createQuery(
            'SELECT r.id, g.nroserie, g.nomyape, g.nroimei, g.estadointervencion, g.valordeclaradocel FROM 
             PresisRetiroBundle:Retiro r,
             TrackerBundle:Tracker t,
             GestionCelBundle:GestionCel g
             WHERE
             t.nroPlanilla = :nroConstancia AND 
             r.id=t.retiro AND
             r.gestioncel = g AND
             (t.detalles is not null AND t.detalles!=:detalles) '
            );
        $query->setParameter('nroConstancia', $nroConstancia);
        $query->setParameter('detalles', "");

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);

    }

}
