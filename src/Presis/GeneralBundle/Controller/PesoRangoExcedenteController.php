<?php

namespace Presis\GeneralBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\GeneralBundle\Entity\PesoRangoExcedente;
use Presis\GeneralBundle\Form\PesoRangoExcedenteType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * PesoRangoExcedente controller.
 *
 */
class PesoRangoExcedenteController extends Controller
{

    public function getRangoPesoExcedenteAction()
    {

        $user=$this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();

        if ($user->hasRole('ROLE_VENDEDOR')){
            $query = $em->createQuery('SELECT pe.id, pe.rangoHasta, pe.costoRangoExcedente, c.empresa, s.descripcion, 
co.descripcion  AS cordonEntrega, cr.descripcion AS cordonRetiro, v.nombre, pe.tipoServicio
        FROM 
        PresisGeneralBundle:PesoRangoExcedente pe,
        PresisGeneralBundle:Cliente c,
        PresisServicioBundle:Servicio s,
        PresisServicioBundle:Cordon co,
        PresisGeneralBundle:Vendedor v,
        PresisServicioBundle:Cordon cr
        WHERE
        pe.cliente=c AND 
        pe.servicio= s AND 
        pe.cordonEntrega=co AND
        pe.cordonRetiro=cr AND 
        c.vendedor=v AND    
        c.vendedor = :vendedor 
        ORDER BY pe.id ASC
        ');
            $query->setParameter('vendedor',$user->getVendedor());
        }else{
            $query = $em->createQuery('SELECT pe.id, pe.rangoHasta, pe.costoRangoExcedente, c.empresa, s.descripcion, 
co.descripcion  AS cordonEntrega, cr.descripcion AS cordonRetiro, pe.tipoServicio
        FROM 
        PresisGeneralBundle:PesoRangoExcedente pe,
        PresisGeneralBundle:Cliente c,
        PresisServicioBundle:Servicio s,
        PresisServicioBundle:Cordon co,
        PresisServicioBundle:Cordon cr
        WHERE
        pe.cliente=c AND 
        pe.servicio= s AND 
        pe.cordonEntrega=co AND
        pe.cordonRetiro=cr
        ORDER BY pe.id ASC
        ');
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        //die($json);

        /*return $this->render('PresisGeneralBundle:BultoExcedente:index.html.twig', array(
            'entities' => $query,
        ));*/

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);

    }

    /**
     * Lists all PesoRangoExcedente entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisGeneralBundle:PesoRangoExcedente')->findAll();

        return $this->render('PresisGeneralBundle:PesoRangoExcedente:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PesoRangoExcedente entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PesoRangoExcedente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pesorangoexcedente_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisGeneralBundle:PesoRangoExcedente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PesoRangoExcedente entity.
     *
     * @param PesoRangoExcedente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PesoRangoExcedente $entity)
    {
        $form = $this->createForm(new PesoRangoExcedenteType(), $entity, array(
            'action' => $this->generateUrl('pesorangoexcedente_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PesoRangoExcedente entity.
     *
     */
    public function newAction()
    {
        $entity = new PesoRangoExcedente();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisGeneralBundle:PesoRangoExcedente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PesoRangoExcedente entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:PesoRangoExcedente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PesoRangoExcedente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:PesoRangoExcedente:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PesoRangoExcedente entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:PesoRangoExcedente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PesoRangoExcedente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:PesoRangoExcedente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PesoRangoExcedente entity.
    *
    * @param PesoRangoExcedente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PesoRangoExcedente $entity)
    {
        $form = $this->createForm(new PesoRangoExcedenteType(), $entity, array(
            'action' => $this->generateUrl('pesorangoexcedente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PesoRangoExcedente entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:PesoRangoExcedente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PesoRangoExcedente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pesorangoexcedente_edit', array('id' => $id)));
        }

        return $this->render('PresisGeneralBundle:PesoRangoExcedente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a PesoRangoExcedente entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisGeneralBundle:PesoRangoExcedente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PesoRangoExcedente entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pesorangoexcedente'));
    }

    /**
     * Creates a form to delete a PesoRangoExcedente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pesorangoexcedente_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
