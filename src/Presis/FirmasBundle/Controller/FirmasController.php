<?php

namespace Presis\FirmasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\FirmasBundle\Entity\Firmas;
use Presis\FirmasBundle\Form\FirmasType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Firmas controller.firmas:
path:     /
defaults: { _controller: "FirmasBundle:Firmas:index" }
 *
 */
class FirmasController extends Controller
{

    public function asAjaxAction(Request $request)
    {

        $fechaDesde = $request->request->get('desde');
        $fechaHasta = $request->request->get('hasta');
        $id_distribuidor = $request->request->get('distribuidor');
        $tracking = $request->request->get('tracking');
        $estado = $request->request->get('estado');

        $limite = $request->request->get('limite');
        $pagina = $request->request->get('pagina');


        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository("FirmasBundle:Firmas")->createQueryBuilder('r');

        if($fechaDesde) {
            $fechaDesde = \DateTime::createFromFormat('d/m/Y', $fechaDesde);
            $entities->andWhere('r.fechaCel >= :desde')
                ->setParameter('desde', $fechaDesde->format('Y-m-d 00:00:00'));
        }

        if($fechaHasta) {
            $fechaHasta = \DateTime::createFromFormat('d/m/Y', $fechaHasta);
            $entities->andWhere('r.fechaCel <= :hasta')
                ->setParameter('hasta', $fechaHasta->format('Y-m-d 23:59:59'));
        }

        if($tracking) {
            $entities->andWhere('r.tracking = :tracking')
                ->setParameter('tracking', $tracking);
        }

        if($id_distribuidor) {
            $entities->andWhere('r.distribuidorId = :distribuidor_id')
                ->setParameter('distribuidor_id', $id_distribuidor);
        }

        if($estado) {
            $entities->andWhere('r.codEstado = :estado')
                ->setParameter('estado', $estado);
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

        //CODIGO PARA VER IMAGENES BLOB
        /*$em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository("FirmasBundle:Firmas")->createQueryBuilder('r');
        $images = array();
        foreach ($entities as $key => $entity) {
            $images[$key] = base64_encode(stream_get_contents($entity->getImg()));
        }
        return $this->render('FirmasBundle:Firmas:index.html.twig', array(
            'entities' => $entities,
            'images' => $images,
        ));*/
    }

    public function asAjaxManifiestosAction(Request $request)
    {

        /*$fechaDesde = $request->request->get('desde');
        $fechaHasta = $request->request->get('hasta');
        $id_distribuidor = $request->request->get('distribuidor');
        $tracking = $request->request->get('tracking');
        $estado = $request->request->get('estado');*/

        $limite = $request->request->get('limite');
        $pagina = $request->request->get('pagina');


        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository("FirmasBundle:FirmasManifiesto")->createQueryBuilder('r');

        /*if($fechaDesde) {
            $fechaDesde = \DateTime::createFromFormat('d/m/Y', $fechaDesde);
            $entities->andWhere('r.fechaCel >= :desde')
                ->setParameter('desde', $fechaDesde->format('Y-m-d 00:00:00'));
        }

        if($fechaHasta) {
            $fechaHasta = \DateTime::createFromFormat('d/m/Y', $fechaHasta);
            $entities->andWhere('r.fechaCel <= :hasta')
                ->setParameter('hasta', $fechaHasta->format('Y-m-d 23:59:59'));
        }

        if($tracking) {
            $entities->andWhere('r.tracking = :tracking')
                ->setParameter('tracking', $tracking);
        }

        if($id_distribuidor) {
            $entities->andWhere('r.distribuidorId = :distribuidor_id')
                ->setParameter('distribuidor_id', $id_distribuidor);
        }

        if($estado) {
            $entities->andWhere('r.codEstado = :estado')
                ->setParameter('estado', $estado);
        }*/

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

    public function imgFirmaAction(Request $request){

        $tracking = $request->request->get('tracking');

        //$tracking = "100006636";

        $em = $this->getDoctrine()->getManager();
        /*$entities = $em->getRepository("FirmasBundle:Firmas")->createQueryBuilder('r');
        $images = array();
        foreach ($entities as $key => $entity) {
            $images[$key] = base64_encode(stream_get_contents($entity->getImg()));
        }*/

        /*$data = $em->getRepository('FirmasBundle:Firmas')->findOneBy(
            array('tracking' => $tracking));*/

        $query = $em->getRepository("FirmasBundle:Firmas")->createQueryBuilder('r')
            ->leftJoin("FotoBundle:Fotos", "f")
            ->where("r.tracking=f.tracking")
            ->getQuery()
            ->getResult();

        $img= base64_encode(stream_get_contents($query->img));

        return $this->render('FirmasBundle:Firmas:show.html.twig', array(
            'data' => $query,
            'img' => $img,
        ));

    }

    /**
     * Lists all Firmas entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FirmasBundle:Firmas')->findAll();

        $query = $em->createQuery('SELECT p.id, p.codigo, p.apellido, p.nombre FROM DistribuidorBundle:Distribuidor p');
        $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

        $query2 = $em->createQuery('SELECT e.codigo, e.nombre FROM EstadoBundle:Estado e');
        $result2 = $query2->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

        return $this->render('FirmasBundle:Firmas:index.html.twig', array(
            'entities' => $entities,
            'distribuidores' => $result,
            'estados' => $result2,
        ));
    }

    /**
     * Lists all Firmas manifiestos entities.
     *
     */
    public function indexManifiestosAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('FirmasBundle:Firmas:index-manifiestos.html.twig');
    }

    /**
     * Creates a new Firmas entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Firmas();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('firmas_show', array('id' => $entity->getId())));
        }

        return $this->render('FirmasBundle:Firmas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Firmas entity.
     *
     * @param Firmas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Firmas $entity)
    {
        $form = $this->createForm(new FirmasType(), $entity, array(
            'action' => $this->generateUrl('firmas_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Firmas entity.
     *
     */
    public function newAction()
    {
        $entity = new Firmas();
        $form   = $this->createCreateForm($entity);

        return $this->render('FirmasBundle:Firmas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Firmas entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FirmasBundle:Firmas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Firmas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FirmasBundle:Firmas:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Firmas entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FirmasBundle:Firmas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Firmas entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FirmasBundle:Firmas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Firmas entity.
    *
    * @param Firmas $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Firmas $entity)
    {
        $form = $this->createForm(new FirmasType(), $entity, array(
            'action' => $this->generateUrl('firmas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Firmas entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FirmasBundle:Firmas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Firmas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('firmas_edit', array('id' => $id)));
        }

        return $this->render('FirmasBundle:Firmas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Firmas entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FirmasBundle:Firmas')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Firmas entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('firmas'));
    }

    /**
     * Creates a form to delete a Firmas entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('firmas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
