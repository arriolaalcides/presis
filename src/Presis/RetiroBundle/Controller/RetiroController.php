<?php

namespace Presis\RetiroBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Presis\GeneralBundle\Entity\ManifiestoCarga;
use Presis\GuiaBundle\Entity\Guia;
use Presis\RetiroBundle\Entity\Sender;
use Presis\RetiroBundle\Form\RetiroConfirmType;
use Presis\DatosEnviosBundle\Entity\DatosEnvios;
use Ps\PdfBundle\Annotation\Pdf;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\RetiroBundle\Entity\Retiro;
use Presis\RetiroBundle\Form\RetiroType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
/**
 * Retiro controller.
 *
 */
class RetiroController extends Controller
{
    /**
     * Returns a Retiro as json
     *
     */
    public function asajaxAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();

        $trackingNo = $request->query->get('tracking');

        $json = '';
        if ($trackingNo != '') {
            $em = $this->getDoctrine()->getManager();

            $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->findOneBy(array('id' => $trackingNo));

            if(!$retiro){
                return new Response("El retiro $trackingNo no existe", Response::HTTP_CONFLICT);
            }

            /*if($retiro->getDatosEnvios()->getConfirmada()!= TRUE){
                return new Response("El retiro $trackingNo no existe", Response::HTTP_CONFLICT);
            }*/

            if($retiro->getCreateGuia()!=$user){

                if($user->hasRole('ROLE_SUCURSAL')){
                    if (!$this->sucursalPermitida($retiro)) {
                        return new Response("No se permite el acceso al retiro $trackingNo, sucursal incorrecta", Response::HTTP_CONFLICT);
                    }
                }
                if($user->hasRole('ROLE_DISTRIBUIDOR')){
                    if (!$this->distribuidorPermitido($retiro)) {
                        return new Response("No se permite el acceso al retiro $trackingNo", Response::HTTP_CONFLICT);
                    }
                }
            }

            $serializer = $this->get('jms_serializer');
            $json = $serializer->serialize($retiro, "json");
        }

        return new Response($json);
    }

    protected function getPrecioManager()
    {
        return $this->container->get('presis_precio');
    }

    /**
     * Lists all Retiro entities.
     *
     */
    public function indexAction()
    {
        //   $em = $this->getDoctrine()->getManager();

        //   $entities = $em->getRepository('PresisRetiroBundle:Retiro')->findAll();

        return $this->render('PresisRetiroBundle:Retiro:index.html.twig', array(
            'nada' => "hola",
        ));
    }

    public function ajaxAction(Request $request)
    {

        $offset = $request->request->get('page');
        $offset = (!$offset) ? 0 : $offset - 1;
        $limit = $request->request->get('rows');
        if (!$limit) $limit = 10;
        $sort = $request->request->get('sort');
        $order = $request->request->get('order');

        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        /* if ($user->hasRole('ROLE_CLIENTE')) {
             $query = $em->createQuery(
                 'SELECT r.id,r.fechHora ,r.precio,c.apenom,c.calle,c.altura,c.piso,c.dpto,c.cp,r.impreso,r.peso,s.descripcion,e.empresa
                 FROM PresisRetiroBundle:Retiro r,PresisRetiroBundle:Comprador c,PresisServicioBundle:Servicio s,PresisGeneralBundle:Cliente e
                 WHERE r.comprador=c
                 AND r.cliente=:cliente
                 AND r.cliente=e
                 and r.servicio=s
                 '
             );
             $query->setParameter('cliente',$user->getCliente());

         }else{
             if ($user->hasRole('ROLE_VENDEDOR')) {

                 $query = $em->createQuery(
                     'SELECT r.id,r.fechHora ,r.precio,c.apenom,c.calle,c.altura,c.piso,c.dpto,c.cp,r.impreso,r.peso,s.descripcion,e.empresa
                    FROM PresisRetiroBundle:Retiro r,PresisRetiroBundle:Comprador c,PresisServicioBundle:Servicio s,PresisGeneralBundle:Cliente e
                    WHERE r.comprador=c
                    and r.servicio=s
                    AND r.cliente=e
                    AND r.cliente in (select cli from PresisGeneralBundle:Cliente cli where cli.vendedor=:vend)');
                 $query->setParameter('vend',$user->getVendedor());


             }else{
                 $query = $em->createQuery(
                     'SELECT r.id,r.fechHora ,r.precio,c.apenom,c.calle,c.altura,c.piso,c.dpto,c.cp,r.impreso,r.peso,s.descripcion,e.empresa
                     FROM PresisRetiroBundle:Retiro r,PresisRetiroBundle:Comprador c,PresisServicioBundle:Servicio s,PresisGeneralBundle:Cliente e
                     WHERE r.comprador=c
                     AND r.cliente=e
                     and r.servicio=s ORDER BY r.'.$sort.' '.$order.' '
                 );
             }

         }*/
        if ($user->hasRole('ROLE_CLIENTE')) {

            $entities = $em->getRepository("PresisRetiroBundle:Retiro")->createQueryBuilder('r')
                ->where('r.cliente=:cliente');
            $entities->setParameter('cliente', $user->getCliente());

        } else if ($user->hasRole('ROLE_VENDEDOR')) {
            //OJO QUE NO TENGO IDEA SI ESTO FUNCIONA, HAY QUE PROBARLO
            $entities = $em->getRepository("PresisRetiroBundle:Retiro")->createQueryBuilder('r')
                ->join('PresisGeneralBundle:Cliente', 'cliente');
            $entities->andWhere("cliente.vendedor in (:vend)");
            $entities->setParameter("vend", $user->getVendedor());
        } else {
            $entities = $em->getRepository("PresisRetiroBundle:Retiro")->createQueryBuilder('r');
        }


        if ($offset)
            $entities->setFirstResult($offset * $limit);

        if ($limit)
            $entities->setMaxResults($limit);


        if ($sort && $order)
            $entities->orderBy("r.$sort", $order);

        $paginator = new Paginator($entities, $fetchJoinCollection = false);
        $result["total"] = count($paginator);
        $result["rows"] = $entities->getQuery()
            ->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);

        /*$retiros = $query->getResult();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($retiros, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);*/


        /* $em = $this->getDoctrine()->getManager();
         $user=$this->get('security.context')->getToken()->getUser();
         if ($user->hasRole('ROLE_CLIENTE')) {
             $query = $em->createQuery(
                 'SELECT r.id,r.fechHora ,r.precio,c.apenom,c.calle,c.altura,c.piso,c.dpto,c.cp,r.impreso,r.peso,s.descripcion,e.empresa
                 FROM PresisRetiroBundle:Retiro r,PresisRetiroBundle:Comprador c,PresisServicioBundle:Servicio s,PresisGeneralBundle:Cliente e
                 WHERE r.comprador=c
                 AND r.cliente=:cliente
                 AND r.cliente=e
                 and r.servicio=s
                 '
             );
             $query->setParameter('cliente',$user->getCliente());

         }else{
             if ($user->hasRole('ROLE_VENDEDOR')) {

                 $query = $em->createQuery(
                     'SELECT r.id,r.fechHora ,r.precio,c.apenom,c.calle,c.altura,c.piso,c.dpto,c.cp,r.impreso,r.peso,s.descripcion,e.empresa
                    FROM PresisRetiroBundle:Retiro r,PresisRetiroBundle:Comprador c,PresisServicioBundle:Servicio s,PresisGeneralBundle:Cliente e
                    WHERE r.comprador=c
                    and r.servicio=s
                    AND r.cliente=e
                    AND r.cliente in (select cli from PresisGeneralBundle:Cliente cli where cli.vendedor=:vend)');
                 $query->setParameter('vend',$user->getVendedor());


             }else{
                 $query = $em->createQuery(
                     'SELECT r.id,r.fechHora ,r.precio,c.apenom,c.calle,c.altura,c.piso,c.dpto,c.cp,r.impreso,r.peso,s.descripcion,e.empresa
                     FROM PresisRetiroBundle:Retiro r,PresisRetiroBundle:Comprador c,PresisServicioBundle:Servicio s,PresisGeneralBundle:Cliente e
                     WHERE r.comprador=c
                     AND r.cliente=e

                     and r.servicio=s
                     '
                 );
             }

         }


         $retiros = $query->getResult();
         $serializer = $this->get('jms_serializer');
         $json=$serializer->serialize($retiros, "json");
         $datos='{"data":[';
         $json=substr($json,1,strlen($json));
         $json=$datos.$json."}";
         return new Response($json);*/
    }

    /**
     * Creates a new Retiro entity.
     *
     */

    public function createAction(Request $request)
    {


        $entity = new Retiro();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        //$form->get('servicio')->addError(new FormError('error message'));
        if ($form->isValid()) {
            $securityContext = $this->container->get('security.context');
            $user = $securityContext->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $totpeso = 0;
            $pre = 0;
            if (0 !== count($entity->getProductos())) {
                foreach ($entity->getProductos() as $producto) {
                    $producto->setRetiro($entity);
                    if ($producto->getFormaCarga() == 2) {
                        $totpeso += $producto->getPeso();
                    }
                    if ($producto->getFormaCarga() == 1) {
                        $totpeso += $this->getPrecioManager()->calcularPesoVolumetrico($producto->getLargo(), $producto->getProfundidad(), $producto->getAlto(), $user->getCliente()->getAforo());

                    }
                    if ($producto->getFormaCarga() == 3) {
                        $totpeso += $this->getPrecioManager()->getPesoByCategoria($producto->getCategoria()->getNombre());

                    }
                    if ($producto->getFormaCarga() == 4) {
                        $totpeso += $this->getPrecioManager()->calcularMayorPeso($producto->getLargo(), $producto->getProfundidad(), $producto->getAlto(), $producto->getPeso(), $user->getCliente()->getAforo());


                    }
                }

                $pre = $this->getPrecioManager()->calcularPrecio($entity->getServicio(), $totpeso, $user->getCliente(), $entity->getSucursal(), $entity->getComprador()->getCp());


            }
            if (gettype($pre) == "integer") {
                $error = new FormError("No se pudo calcular el precio. Comuniquese con su ejecutivo de cuentas");
                $form->addError($error);
                return $this->render('PresisRetiroBundle:Retiro:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
            }
            $sucu = $entity->getSucursal();
            $sender = $this->setSenderData($sucu);

            $entity->setSender($sender);
            $entity->setPeso($totpeso);
            $entity->setPrecio($pre->getPrecio());
            $entity->setRango($pre->getRango());
            $entity->setCliente($user->getCliente());
            $entity->setFechHora(new \DateTime('now'));
            $dt = new DatosEnvios();
            $dt->setCantreal($form["cantidad"]->getData());

            $entity->setDatosEnvios = $dt;
            $repoCordon = $this->getDoctrine()->getRepository('PresisServicioBundle:CpCordon');
            //$cordon=$repoCordon->findCordonInCp($cp);
            $cpcordon = $repoCordon->findOneByCp($entity->getComprador()->getCp());

            $entity->setCordonEntrega($cpcordon->getCordon());
            $entity->setPrestador($this->getPrecioManager()->generatePrestador($cpcordon));
            //  $em->persist($entity);
            // $em->flush();
            $response = $this->forward('PresisRetiroBundle:Retiro:confirm', array(
                'retiro' => $entity,

            ));
            return $response;
        }
        //$form->get('cantidad')->setData($dt->getCantreal());
        return $this->render('PresisRetiroBundle:Retiro:new.html.twig', array(
            'entity' => $entity,
            //'cant' => $dt->getCantidad(),
            'form' => $form->createView(),
        ));
    }

    public function saveAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new Retiro();

        $form = $this->createForm(new RetiroConfirmType(), $entity, array(
            'action' => $this->generateUrl('retiro_save'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $sucu = $entity->getSucursal();
        $sender = $this->setSenderData($sucu);
        $entity->setSender($sender);


        $entity->setCliente($user->getCliente());
        $entity->setFechHora(new \DateTime('now'));
        $repoCordon = $this->getDoctrine()->getRepository('PresisServicioBundle:CpCordon');
        //$cordon=$repoCordon->findCordonInCp($cp);
        $cpcordon = $repoCordon->findOneByCp($entity->getComprador()->getCp());


        $entity->setCordonEntrega($cpcordon->getCordon());
        $entity->setPrestador($this->getPrecioManager()->generatePrestador($cpcordon));
        $em->persist($entity);
        $em->flush();

        //FUNCION PICCINI PARA IR A CALCULAR COSTOS
        $response = $this->forward('DatosEnviosBundle:DatosEnvios:costosEcommerce', array(
            'id' => $entity->getId(),
        ));

        return $this->render('PresisRetiroBundle:Retiro:index.html.twig', array(
            'nada' => "hola",
        ));

    }

    public function confirmAction($retiro)
    {
        $form = $this->createForm(new RetiroConfirmType(), $retiro, array(
            'action' => $this->generateUrl('retiro_save'),
            'method' => 'POST',
            'read_only' => true,
        ));

        $form->add('submit', 'submit', array('label' => 'Confirmar Retiro', 'attr' => array('class' => 'btn btn-success')));

        return $this->render('PresisRetiroBundle:Retiro:confirm.html.twig', array(
            'entity' => $retiro,
            'form' => $form->createView(),

        ));

    }

    public function ajaxHabilitadosAction(Request $request)
    {
        $cp = $request->get("cp");
        $suc = $request->get("sucursal");

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $q = $em->getRepository("PresisServicioBundle:Servicio")->findHabilitados($user, $cp, $suc);
        $servicios = $q->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize($servicios, 'json');
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
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new RetiroType($securityContext), $entity, array(
            'action' => $this->generateUrl('retiro_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Solicitar Retiro', 'attr' => array('class' => 'btn btn-success')));

        return $form;
    }

    /**
     * Finds and displays a Guia entity.
     * @Pdf()
     */
    public function generarAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $ids = $request->get('ids');
        $cont = 0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal = array();
        $guia = new Guia();
        $arra = new ArrayCollection();
        $guia->setFechahora(new \DateTime('now'));
        foreach ($ids as $id) {

            $cont++;
            if ($cont == 1) {
                $arrpare = array();
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
                'attr' => array('target' => '_blank'),
            ));
        }
        if($empresa=='maslogistica'){
            return $this->render(sprintf('PresisGuiaBundle:Guia:guiaMasLogistica.%s.twig', $format), array(
                'entity' => $guia,
                'attr' => array('target' => '_blank'),
            ));
        }

    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showPendientesAction()
    {
        $format = $this->get('request')->get('_format');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("PresisRetiroBundle:Retiro");
        $user = $this->get('security.context')->getToken()->getUser();
        $cont = 0;
        $arrtotal = array();

        $vouchers = $repo->findBy(array('impreso' => 'false', 'cliente' => $user->getCliente()));
        foreach ($vouchers as $voucher) {
            $cont++;
            if ($cont == 1) {
                $arrpare = array();
            }

            ///   $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);
            //$entity=$repo->findById($voucher->getId());
            //PICCINI - 02-01-17 LO SACO PARA MARCAR LA GUIA AL MOMENTO DE ASIGNAR CHOFER DE RETIRO.
            //$voucher->setImpreso(true);
            $em->persist($voucher);
            array_push($arrpare, $voucher);
            if ($cont == 2) {
                array_push($arrtotal, $arrpare);

                $cont = 0;
            }
        }
        if ($cont == 1) {
            array_push($arrtotal, $arrpare);

        }
        $em->flush();


        //  $format="pdf";
        return $this->render(sprintf('PresisRetiroBundle:Default:dupl.%s.twig', $format), array(
            'vouchers' => $arrtotal,
            'attr' => array('target' => '_blank'),
        ));
    }

    /**
     * Displays a form to create a new Retiro entity.
     *
     */

    public function newAction()
    {

        $entity = new Retiro();
        $form = $this->createCreateForm($entity);

        return $this->render('PresisRetiroBundle:Retiro:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showEtiAction()
    {

        $empresa = $this->container->getParameter('empresa');

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $ids = $request->get('ids');
        $cont = 0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal = array();

        $arra = new ArrayCollection();
        $arrpa2 = array();

        foreach ($ids as $id) {

            $cont++;
            if ($cont == 1) {
                $arrpare = array();
            }

            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            $arra->add($entity);
            //PICCINI - 02-01-17 LO SACO PARA MARCAR LA GUIA AL MOMENTO DE ASIGNAR CHOFER DE RETIRO.
            //$entity->setImpreso(true);
            $em->persist($entity);
            array_push($arrpare, $entity);
            array_push($arrpa2, $entity);

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
        if($empresa=='fasttrack'){
            return $this->render(sprintf('PresisRetiroBundle:Default:eti.%s.twig', $format), array(
                'vouchers' => $arrpa2,
                'attr' => array('target' => '_blank'),
            ));
        }
        if($empresa=='maslogistica'){
            return $this->render(sprintf('PresisRetiroBundle:Default:etiMasLogistica.%s.twig', $format), array(
                'vouchers' => $arrpa2,
                'attr' => array('target' => '_blank'),
            ));
        }



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

            $arrpare=array();
            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            $arra->add($entity);
            //PICCINI - 02-01-17 LO SACO PARA MARCAR LA GUIA AL MOMENTO DE ASIGNAR CHOFER DE RETIRO.
            //$entity->setImpreso(true);
            $em->persist($entity);
            array_push($arrpare,$entity);
            array_push($arrpare,$entity);

            array_push($arrtotal,$arrpare);


        }


        $em->flush();


        $format = $this->get('request')->get('_format');
        //  $format="pdf";

        $empresa = $this->container->getParameter('empresa');

        if($empresa=='fasttrack'){
            return $this->render(sprintf('PresisRetiroBundle:Default:dupl.%s.twig', $format), array(
                'vouchers' => $arrtotal,
                'attr'=>array('target'=>'_blank'),
            ));
        }

        if($empresa=='maslogistica'){
            return $this->render(sprintf('PresisRetiroBundle:Default:duplMasLogistica.%s.twig', $format), array(
                'vouchers' => $arrtotal,
                'attr'=>array('target'=>'_blank'),
            ));
        }


    }

    /*public function showVoucherAction()
    {

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

            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            $arra->add($entity);
            $entity->setImpreso(true);
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
        return $this->render(sprintf('PresisRetiroBundle:Default:dupl.%s.twig', $format), array(
            'vouchers' => $arrtotal,
            'attr' => array('target' => '_blank'),
        ));


    }*/

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showAction($id = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);
        $format = $this->get('request')->get('_format');
        //  $format="pdf";
        return $this->render(sprintf('PresisRetiroBundle:Default:dupl.%s.twig', $format), array(
            'retiro' => $entity,
            'attr' => array('target' => '_blank'),
        ));

    }

    /**
     * Displays a form to edit an existing Retiro entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retiro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:Retiro:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Retiro entity.
     *
     * @param Retiro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Retiro $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new RetiroType($securityContext), $entity, array(
            'action' => $this->generateUrl('retiro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    public function setSenderData($sucursal)
    {
        $sender = new Sender();

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

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retiro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('retiro_edit', array('id' => $id)));
        }

        return $this->render('PresisRetiroBundle:Retiro:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Retiro entity.
     *
     */
    public function eliminarAction(Request $request, $id_retiro)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);

        if($user==$retiro->getCreateGuia()){
            $em->remove($retiro);
            $em->flush();
            return new Response("ok");
        }else{
            return new Response("error");
        }
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
     * Creates a form to delete a Retiro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('retiro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showguiaAction()
    {

        $request = $this->container->get('request_stack')->getCurrentRequest();

        $id = $request->get('id');
        $canal = $request->get('canal');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retiro entity.');
        }

        //PICCINI - 04-01-17 LO MARCO SOLO CUANDO LA IMPRESION VIENE DE RETIRO
        if($canal=='retiro'){
            $entity->setImpreso(true);
        }
        $em->persist($entity);
        $em->flush();

        $cpCordonEntrega = null;
        if ($entity->getComprador()) {
            $cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                array('cp' => $entity->getComprador()->getCp()));
        }

        $format = $this->get('request')->get('_format');

        $empresa = $this->container->getParameter('empresa');

        if($empresa=='fasttrack'){

            return $this->render(sprintf('PresisRetiroBundle:Default:guia.%s.twig', $format), array(
                'retiro' => $entity,
                'zona' => ($cpCordonEntrega) ? $cpCordonEntrega->getZona() : "",
                'attr' => array('target' => '_blank'),
            ));
        }
        if($empresa=='maslogistica'){
            return $this->render(sprintf('PresisRetiroBundle:Default:guiaMasLogistica.%s.twig', $format), array(
                'retiro' => $entity,
                'zona' => ($cpCordonEntrega) ? $cpCordonEntrega->getZona() : "",
                'attr' => array('target' => '_blank'),
            ));
        }


    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showetiquetaAction()
    {

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $request = $this->container->get('request_stack')->getCurrentRequest();

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retiro entity.');
        }
        //PICCINI - 02-01-17 LO SACO PARA MARCAR LA GUIA AL MOMENTO DE ASIGNAR CHOFER DE RETIRO.
        //$entity->setImpreso(true);
        $em->persist($entity);
        $em->flush();

        $cpCordonEntrega = null;
        if ($entity->getComprador()) {
            $cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
                array('cp' => $entity->getComprador()->getCp()));
        }

        $format = $this->get('request')->get('_format');

        $var = $this->container->getParameter('empresa');

        if($var=='fasttrack'){
            return $this->render(sprintf('PresisRetiroBundle:Default:etiquetaFT.%s.twig', $format), array(
                'retiro' => $entity,
                'zona' => ($cpCordonEntrega) ? $cpCordonEntrega->getZona() : "",
                'attr' => array('target' => '_blank'),
            ));
        }
        if($var=='maslogistica'){
            if($entity->getCliente()=='ELEPANTS'){
                return $this->render(sprintf('PresisRetiroBundle:Default:etiquetaMasLogisticaElepants.%s.twig', $format), array(
                    'retiro' => $entity,
                    'zona' => ($cpCordonEntrega) ? $cpCordonEntrega->getZona() : "",
                    'attr' => array('target' => '_blank'),
                ));
            }else{
                return $this->render(sprintf('PresisRetiroBundle:Default:etiquetaMasLogistica.%s.twig', $format), array(
                    'retiro' => $entity,
                    'zona' => ($cpCordonEntrega) ? $cpCordonEntrega->getZona() : "",
                    'attr' => array('target' => '_blank'),
                ));
            }

        }
        if($var=='caktus'){
            return $this->render(sprintf('PresisRetiroBundle:Default:etiquetaFT.%s.twig', $format), array(
                'retiro' => $entity,
                'zona' => ($cpCordonEntrega) ? $cpCordonEntrega->getZona() : "",
                'attr' => array('target' => '_blank'),
            ));
        }
    }

    /*============================IMPRIME UNA SELECCION DE GUIAS===========================================*/
    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showguiasAction()
    {

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $ids = $request->get('ids');
        $cont = 0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal = array();
        $zonas = array();

        $arra = new ArrayCollection();

        foreach ($ids as $id) {

            $cont++;
            if ($cont == 1) {
                $arrpare = array();
            }

            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            $arra->add($entity);
            //PICCINI - 02-01-17 LO SACO PARA MARCAR LA GUIA AL MOMENTO DE ASIGNAR CHOFER DE RETIRO.
            //$entity->setImpreso(true);
            $em->persist($entity);
            array_push($arrpare, $entity);
            if ($cont == 2) {
                array_push($arrtotal, $arrpare);
                $cont = 0;
            }

            $cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(array('cp' => $entity->getComprador()->getCp()));
            array_push($zonas, ($cpCordonEntrega) ? $cpCordonEntrega->getZona() : "");
        }
        if ($cont == 1) {
            array_push($arrtotal, $arrpare);
        }
        $em->flush();

        $format = $this->get('request')->get('_format');
        //  $format="pdf";

        $empresa = $this->container->getParameter('empresa');

        if($empresa=='fasttrack'){
            return $this->render(sprintf('PresisRetiroBundle:Default:guia2.%s.twig', $format), array(
                'guias' => $arrtotal,
                'zonas' => $zonas,
                'attr' => array('target' => '_blank'),
            ));
        }
        if($empresa=='maslogistica'){
            return $this->render(sprintf('PresisRetiroBundle:Default:guia2MasLogistica.%s.twig', $format), array(
                'guias' => $arrtotal,
                'zonas' => $zonas,
                'attr' => array('target' => '_blank'),
            ));
        }
    }

    /*============================IMPRIME UNA SELECCION DE ETIQUETAS===========================================*/
    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showetiquetasAction()
    {

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $ids = $request->get('ids');

        $cont = 0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal = array();
        $zonas = array();

        $arra = new ArrayCollection();

        foreach ($ids as $id) {

            $cont++;
            if ($cont == 1) {
                $arrpare = array();
            }

            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            $arra->add($entity);
            //PICCINI - 02-01-17 LO SACO PARA MARCAR LA GUIA AL MOMENTO DE ASIGNAR CHOFER DE RETIRO.
            //$entity->setImpreso(true);
            $em->persist($entity);
            array_push($arrpare, $entity);
            if ($cont == 2) {
                array_push($arrtotal, $arrpare);
                $cont = 0;
            }

            $cpCordonEntrega = $em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(array('cp' => $entity->getComprador()->getCp()));
            array_push($zonas, ($cpCordonEntrega) ? $cpCordonEntrega->getZona() : "");
        }
        if ($cont == 1) {
            array_push($arrtotal, $arrpare);
        }
        $em->flush();

        $format = $this->get('request')->get('_format');
        //  $format="pdf";

        $empresa = $this->container->getParameter('empresa');

        if($empresa=='fasttrack'){
            return $this->render(sprintf('PresisRetiroBundle:Default:etiquetaFT2.%s.twig', $format), array(
                'guias' => $arrtotal,
                'zonas' => $zonas,
                'attr' => array('target' => '_blank'),
            ));
        }
        if($empresa=='maslogistica'){
            return $this->render(sprintf('PresisRetiroBundle:Default:etiquetaMasLogistica2.%s.twig', $format), array(
                'guias' => $arrtotal,
                'zonas' => $zonas,
                'attr' => array('target' => '_blank'),
            ));
        }
    }

    private function sucursalPermitida(\Presis\RetiroBundle\Entity\Retiro $retiro){
        $em = $this->getDoctrine()->getManager();
        $result = true;
        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $sucursalLogueada = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;
        //die($sucursalLogueada);
        if($sucursalLogueada) {
            $nroPlanilla = ($retiro->getNroPlanilla()?$retiro->getNroPlanilla():0);
            $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($nroPlanilla);

            if($recorrido) {
                //die("RECORRIDO: ".$recorrido->getDistribuidor()."--"." LOGUEADO: ".$sucursalLogueada);
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
            if($nroPlanilla==0){
                return false;
            }
            $recorrido = $em->getRepository('RecorridoBundle:Recorrido')->find($nroPlanilla);
            if($recorrido) {
                if ($recorrido->getDistribuidor() != $distribuidorLogueado) {
                    return false;
                }
            }
        }
        return $result;
    }

    public function emailAction(Request $request){
        $tracking_id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($tracking_id);
        $retiro->setSendMail(true);

        die($retiro->getDistribuidor());

        $em->persist($retiro);
        $em->flush();
    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showconstanciaAction()
    {

        $em = $this->getDoctrine()->getManager();
        $ultimoManifiesto = $em->createQueryBuilder()
            ->select('MAX(e.id)')
            ->from('PresisGeneralBundle:ManifiestoCarga', 'e')
            ->getQuery()
            ->getSingleScalarResult();
       $nroManifiesto = $ultimoManifiesto+1;

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $ids = $request->get('ids');
        $cont = 0;
        $limit = 1;
        $arrtotal = array();
        $zonas = array();
        $arra = new ArrayCollection();
        foreach ($ids as $id) {
            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            if($entity->getSubZonaOrigen()!='INT'){
                $cont++;
                if ($cont == 1) {
                    $arrpare = array();
                }

                $arra->add($entity);
                $entity->setNroConstancia($nroManifiesto);
                $em->persist($entity);
                array_push($arrpare, $entity);
                if ($cont == 2) {
                    array_push($arrtotal, $arrpare);
                    $cont = 0;
                }
            }
        }
        if ($cont == 1) {
            array_push($arrtotal, $arrpare);
        }

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $manifiestoCarga = new ManifiestoCarga();
        $manifiestoCarga->setUsuario($user);

        if($user->getCliente()->getEmpresa()){
            $manifiestoCarga->setCliente($user->getCliente()->getEmpresa());
        }else{
            $manifiestoCarga->setCliente("SISTEMA");
        }
        $manifiestoCarga->setFecha(new \Datetime());

        $dataSuc = $em->getRepository('PresisGeneralBundle:Sucursal')->find($user->getSucursal());

        $em->persist($manifiestoCarga);
        $em->flush();
        $format = $this->get('request')->get('_format');
        //  $format="pdf";
        return $this->render(sprintf('PresisRetiroBundle:Default:constancia.%s.twig', $format), array(
            'retiro' => $entity,
            'guias' => $arrtotal,
            'dataSuc' => $dataSuc,
            'attr' => array('target' => '_blank'),
        ));
    }

    /**
     * Finds and displays a Retiro entity.
     * @Pdf()
     */
    public function showconstancia2Action()
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $request = $this->container->get('request_stack')->getCurrentRequest();

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        //$entity = $em->getRepository('PresisRetiroBundle:Retiro')->findBy(array('nroConstancia'=>$id));

        $estado = $em->getRepository('EstadoBundle:Estado')->find('157');

        $query = $em->createQuery(
            'SELECT 
              m.descripcion AS modelodescripcion, f.descricion AS fabricantedescripcion, r.id, r.fechHora, s.empresa, s.calle, s.altura, s.piso, s.dpto, g.trayecto, g.nroserie, g.nomyape, g.nroimei, g.estadointervencion, 
              g.valordeclaradocel, s.localidad, s.provincia, g.tiposervicio, g.precinto, se.descripcion,
              c.empresa AS compEmpresa, c.apenom, c.calle AS compCalle, c.altura AS compAltura, c.piso AS compPiso, c.dpto AS compDpto,
              c.localidad AS compLocalidad, c.provincia AS compProvincia, g.valordeclaradocel, d.peso
              FROM 
             PresisRetiroBundle:Retiro r,
             TrackerBundle:Tracker t,
             GestionCelBundle:GestionCel g,
             PresisRetiroBundle:Sender s,
             DatosEnviosBundle:DatosEnvios d,
             PresisServicioBundle:Servicio se,
             PresisRetiroBundle:Comprador c,
             MovistarBundle:Modelo m,
             MovistarBundle:Fabricante f
             WHERE
             t.nroPlanilla = :nroConstancia AND 
             r.id=t.retiro AND
             r.gestioncel = g AND
             r.sender = s AND
             r.datosEnvios = d AND 
             d.servicio = se AND
             r.comprador = c AND 
             g.modelo = m AND 
             g.fabricante = f AND
             (t.detalles is not null AND t.detalles!=:detalles) '
        );
        $query->setParameter('nroConstancia', $id);
        $query->setParameter('detalles', '');

        $entity = $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

        $dataSuc = $em->getRepository('PresisGeneralBundle:Sucursal')->find($user->getSucursal());

        $format = $this->get('request')->get('_format');

        //  $format="pdf";
        return $this->render(sprintf('PresisRetiroBundle:Default:constancia2.%s.twig', $format), array(
            'manifiesto' => $id,
            'retiro' => $entity,
            'dataSuc' => $dataSuc,
            'attr' => array('target' => '_blank'),
        ));
    }

}
