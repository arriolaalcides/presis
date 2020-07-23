<?php

namespace Presis\ServicioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\ServicioBundle\Entity\Habilitaciones;
use Presis\ServicioBundle\Form\HabilitacionesType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Habilitaciones controller.
 *
 */
class HabilitacionesController extends Controller
{

    public function ajaxAction(){
        $em=$this->getDoctrine()->getManager();
        $consulta = $em->createQueryBuilder()
            ->from('PresisServicioBundle:Habilitaciones', 'h')

            ->select("h.id,ce.descripcion cordonE,cr.descripcion cordonR,s.descripcion,h.horaDesde,h.horaHasta")
            ->join("h.cordonEntrega","ce")
            ->join("h.cordonRetiro","cr")
            ->join("h.servicio","s");





        $consulta=$consulta->getQuery();
        $cpcordones=$consulta->getResult();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($cpcordones, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }
    /**
     * Lists all Habilitaciones entities.
     *
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisServicioBundle:Habilitaciones')->findAll();

        return $this->render('PresisServicioBundle:Habilitaciones:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Habilitaciones entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Habilitaciones();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('habilitaciones_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisServicioBundle:Habilitaciones:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Habilitaciones entity.
     *
     * @param Habilitaciones $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Habilitaciones $entity)
    {
        $form = $this->createForm(new HabilitacionesType(), $entity, array(
            'action' => $this->generateUrl('habilitaciones_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Habilitación','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Habilitaciones entity.
     *
     */
    public function newAction()
    {
        $entity = new Habilitaciones();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisServicioBundle:Habilitaciones:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Habilitaciones entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Habilitaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Habilitaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:Habilitaciones:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Habilitaciones entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Habilitaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Habilitaciones entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:Habilitaciones:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Habilitaciones entity.
    *
    * @param Habilitaciones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Habilitaciones $entity)
    {
        $form = $this->createForm(new HabilitacionesType(), $entity, array(
            'action' => $this->generateUrl('habilitaciones_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Grabar','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Habilitaciones entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Habilitaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Habilitaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('habilitaciones_edit', array('id' => $id)));
        }

        return $this->render('PresisServicioBundle:Habilitaciones:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Habilitaciones entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisServicioBundle:Habilitaciones')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Habilitaciones entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('habilitaciones'));
    }

    /**
     * Creates a form to delete a Habilitaciones entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('habilitaciones_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }
}
