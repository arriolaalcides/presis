<?php

namespace Presis\RendicionBundle\Controller;

use Presis\RendicionBundle\Entity\RendicionRetiro;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\RendicionBundle\Entity\Rendicion;
use Presis\EstadoBundle\Entity\Estado;
use Presis\RendicionBundle\Form\RendicionType;
use Symfony\Component\HttpFoundation\Response;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\TrackerBundle\Entity\Tracker;
use Ps\PdfBundle\Annotation\Pdf;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Rendicion controller.
 *
 */
class RendicionController extends Controller
{

    /**
     * Todos los recorridos (Paginado)
     *
     */
    public function asAjaxAction(Request $request)
    {
        $fechaDesde = $request->request->get('desde');
        $fechaHasta = $request->request->get('hasta');
        $cliente_id = $request->request->get('cliente');
        $nroRendicion = $request->request->get('nroRendicion');

        $limite = $request->request->get('limite');
        $pagina = $request->request->get('pagina');

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        if(!$user->hasRole('ROLE_VENDEDOR')){
            $entities = $em->getRepository("RendicionBundle:Rendicion")->createQueryBuilder('r');
        }
        if($user->hasRole('ROLE_VENDEDOR')){
            $entities = $em->getRepository("RendicionBundle:Rendicion")->createQueryBuilder('r')
                ->join('PresisGeneralBundle:Cliente', 'c')
                ->where('r.cliente = c.id')
                ->andWhere('c.vendedor = :vendedor')
                ->setParameter('vendedor', $user->getVendedor());
        }

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

        if($nroRendicion) {
            $entities->andWhere('r.id = :nroRendicion')
                ->setParameter('nroRendicion', $nroRendicion);
        }

        if($cliente_id) {
            $entities->andWhere('r.cliente = :cliente')
                ->setParameter('cliente', $cliente_id);
        }

        $entities->setFirstResult($pagina * $limite)
            ->setMaxResults($limite);

        $paginator = new Paginator($entities, $fetchJoinCollection = false);
        $result["total"] = count($paginator);
        $result["rows"] = $entities->getQuery()
            ->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }
    /**
     * Todas las rendiciones (Paginado)
     *
     */
    /*public function asAjaxAction(Request $request)
    {
        $fechaDesde = $request->request->get('desde');
        $fechaHasta = $request->request->get('hasta');
        $id_cliente = $request->request->get('id_cliente');
        $offset       = $request->request->get('page');
        $offset = (!$offset)? 0: $offset-1;
        $limit      = $request->request->get('rows');
        if(!$limit) $limit = 10;
        $sort      = $request->request->get('sort');
        $order      = $request->request->get('order');

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository("RendicionBundle:Rendicion")->createQueryBuilder('r');

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

        if($id_cliente) {
            $entities->andWhere('r.cliente = :cliente')
                ->setParameter('cliente', $id_cliente);
        }

        if ($offset)
            $entities->setFirstResult($offset * $limit);

        if ($limit)
            $entities->setMaxResults($limit);


        if($sort && $order)
            $entities->orderBy("r.$sort", $order);

        $paginator = new Paginator($entities, $fetchJoinCollection = false);
        $result["total"] = count($paginator);
        $result["rows"] = $paginator->getQuery()
            ->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }*/

    /**
     * Todas las planillas
     *
     */
    public function indexAction()
    {
        return $this->render('RendicionBundle:Rendicion:index.html.twig');
    }

    /**
     * Returns all Retiros that belongs to a Rendicion
     *
     */
    public function retirosAsAjaxAction()
    {
        $rendicionId = $this->get('request')->request->get('rendicion');

        $json = '[]';
        if($rendicionId != ''){
            $em = $this->getDoctrine()->getManager();

            $rendicion = $em->getRepository('RendicionBundle:Rendicion')->find($rendicionId);

            if($rendicion) {

                $rr = $rendicion->getRendicionesRetiros();
                if($rr->count() > 0) {
                    $serializer = $this->get('jms_serializer');
                    $json = $serializer->serialize($rr, "json");
                }
            }
        }

        return new Response('{"data":'.$json."}");
    }

    /**
     * Creates a new Rendicion entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Rendicion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('RendicionBundle:Rendicion:new-edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'editing' => true,
        ));
    }

    /**
     * Creates a form to create a Rendicion entity.
     *
     * @param Rendicion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Rendicion $entity)
    {
        $form = $this->createForm(new RendicionType(), $entity, array(
            'action' => $this->generateUrl('rendicion_create'),
            'method' => 'POST',
        ));
        //$form->add('submit', 'submit', array('label' => 'NUEVA PLANILLA'));
        return $form;
    }

    /**
     * Displays a form to create a new Rendicion entity.
     *
     */
    public function newAction()
    {
        $entity = new Rendicion();
        $form   = $this->createCreateForm($entity);

        return $this->render('RendicionBundle:Rendicion:new-edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Rendicion entity.
     *@Pdf()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RendicionBundle:Rendicion')->find($id);
        $format = $this->get('request')->get('_format');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rendicion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(sprintf('RendicionBundle:Rendicion:show.%s.twig', $format), array(
            'rendicion' => $entity,
            'retiros'   => $entity->getRetiros(),
            'attr'      => array('target'=>'_blank'),
        ));
    }

    /**
     * Displays a form to edit an existing Rendicion entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RendicionBundle:Rendicion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rendicion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RendicionBundle:Rendicion:new-edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'editing' => true,
        ));
    }

    /**
     * Creates a form to edit a Rendicion entity.
     *
     * @param Rendicion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Rendicion $entity)
    {
        $form = $this->createForm(new RendicionType(), $entity, array(
            'action' => $this->generateUrl('rendicion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Rendicion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RendicionBundle:Rendicion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rendicion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('rendicion_edit', array('id' => $id)));
        }

        return $this->render('RendicionBundle:Rendicion:new-edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'editing' => true,
        ));
    }
    /**
     * Deletes a Rendicion entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RendicionBundle:Rendicion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Rendicion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('rendicion'));
    }

    /**
     * Deletes a Retiro link to the Rendicion entity.
     *
     */
    public function retiroDeleteAction(Request $request, $id_rendicion_retiro, $id_rendicion)
    {
        $em = $this->getDoctrine()->getManager();
        $rendicion = $em->getRepository('RendicionBundle:Rendicion')->find($id_rendicion);

        $rendicion_retiro = $em->getRepository('RendicionBundle:RendicionRetiro')->find($id_rendicion_retiro);

        if (!$rendicion || !$rendicion_retiro) {
            throw $this->createNotFoundException('Unable to find Rendicion or Retiro entity.');
        }

        $rendicion->removeRendicionesRetiro($rendicion_retiro);
        $em->remove($rendicion_retiro);
        $em->persist($rendicion);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($rendicion_retiro, "json");

        return new Response('{"data":'.$json."}");
    }

    /**
     * Devuelve si el retiro se puede agregar a la planilla
     *
     * @param \Presis\RendicionBundle\Entity\Rendicion $rendicion
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return string
     */
    private function retiroSePuedeAgregar(\Presis\RendicionBundle\Entity\Rendicion $rendicion, \Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $respuesta = "";

        if ($retiro->isRendido()) {
            $respuesta = "El retiro ya fue rendido en otra planilla";
        }
        if ($rendicion->getRetiros()->contains($retiro)) {
            $respuesta = "El retiro ".$retiro->getId()." ya fue agregado";
        }
        if ($retiro->getClienteId() != $rendicion->getClienteId()) {
            $respuesta = "El cliente de la planilla difiere del retiro";
        }

        return $respuesta;
    }

    /**
     * Adds a Retiro to the Rendicion entity.
     *
     */
    public function addRetiroAction(Request $request, $id_retiro, $id_rendicion)
    {
        $em = $this->getDoctrine()->getManager();

        $rendicion = $em->getRepository('RendicionBundle:Rendicion')->find($id_rendicion);

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);

        if (!$rendicion || !$retiro) {
            return new Response("El retiro $id_retiro no existe", Response::HTTP_CONFLICT);
        }
        $respuesta = $this->retiroSePuedeAgregar($rendicion, $retiro);

        if(!empty($respuesta)) {
            return new Response($respuesta, Response::HTTP_CONFLICT);
        }

        //die("HOLA: ".$this->container->get('security.context')->getToken()->getUser());

        $rendicionRetiro = new RendicionRetiro($rendicion,$retiro);
        $rendicionRetiro->setUser($this->container->get('security.context')->getToken()->getUser());
        $em->persist($rendicionRetiro);
        $em->flush();


        /*$rendicion->addRetiro($retiro);
        $em->persist($rendicion);
        $em->flush();*/

        /* También actualiza la entidad Tracker */
        $tracker = new Tracker();
        $tracker->setRetiro($retiro);
        $tracker->setUser($this->container->get('security.context')->getToken()->getUser());
        $tracker->setNroPlanilla($id_rendicion);
        $retiro->addHistorico($tracker);
        $em->persist($retiro);
        $em->flush();
        /*=======================================*/

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($retiro, "json");

        return new Response('{"data":'.$json."}");
    }

    /**
     * Devuelve los retiros que tienen la clave remito, que se puede repetir
     *
     */
    public function getRetirosRemitoAction(Request $request, $id_retiro_remito, $id_rendicion)
    {
        $em = $this->getDoctrine()->getManager();
        $rendicion = $em->getRepository('RendicionBundle:Rendicion')->find($id_rendicion);

        $retiros = $em->getRepository('PresisRetiroBundle:Retiro')->findBy(
            array('remito' => $id_retiro_remito),
            array('fechHora' => "DESC"));

        $retirosValidos = null;
        if($retiros) {
            $retirosValidos = array();
            foreach ($retiros as $retiro) {
                if (empty($this->retiroSePuedeAgregar($rendicion, $retiro))) {
                    $retirosValidos[] = $retiro;
                }
            }
        }

        if (!$rendicion || !$retiros || !$retirosValidos) {
            return new Response("No hay disponible un retiro con número de remito $id_retiro_remito", Response::HTTP_CONFLICT);
        }

        $serializer = $this->get('jms_serializer');

        $response = new Response($serializer->serialize($retirosValidos, "json"));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     * Cierra la planilla de rendicion. Cambia el atributo rendido de los retiros
     *
     */
    public function cerrarPlanillaAction($id_rendicion)
    {
        $em = $this->getDoctrine()->getManager();
        $rendicion = $em->getRepository('RendicionBundle:Rendicion')->find($id_rendicion);

        if (!$rendicion) {
            throw $this->createNotFoundException('Unable to find Rendicion entity.');
        }

        $rendicion->setCerrada(true);
        $rendicion->rendirRetiros();
        $em->persist($rendicion);
        $em->flush();

        return new Response();
    }

    /**
     * Creates a form to delete a Rendicion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rendicion_delete', array('id' => $id)))
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
        $rendicion = $em->getRepository('RendicionBundle:Rendicion')->find($id);

        if ($rendicion) {
            return new Response("La planilla existe.");
        }

        return new Response("La planilla no existe", Response::HTTP_CONFLICT);
    }
}