<?php

namespace Presis\ServicioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\ServicioBundle\Entity\Cordon;
use Presis\ServicioBundle\Form\CordonType;
use Symfony\Component\HttpFoundation\Response;


/**
 * Cordon controller.
 *
 */
class CordonController extends Controller
{
    public function ajaxAction(){


        $em=$this->getDoctrine()->getManager();
        $cordones=$em->getRepository("PresisServicioBundle:Cordon")->findAll();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($cordones, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);


    }

    /**
     * Lists all Cordon entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisServicioBundle:Cordon')->findAll();

        return $this->render('PresisServicioBundle:Cordon:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Cordon entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Cordon();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cordon_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisServicioBundle:Cordon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Cordon entity.
     *
     * @param Cordon $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cordon $entity)
    {
        $form = $this->createForm(new CordonType(), $entity, array(
            'action' => $this->generateUrl('cordon_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Cordón','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Cordon entity.
     *
     */
    public function newAction()
    {
        $entity = new Cordon();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisServicioBundle:Cordon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Cordon entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Cordon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cordon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:Cordon:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Cordon entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Cordon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cordon entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:Cordon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Cordon entity.
    *
    * @param Cordon $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Cordon $entity)
    {
        $form = $this->createForm(new CordonType(), $entity, array(
            'action' => $this->generateUrl('cordon_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Grabar','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Cordon entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Cordon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cordon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cordon_edit', array('id' => $id)));
        }

        return $this->render('PresisServicioBundle:Cordon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Cordon entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisServicioBundle:Cordon')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cordon entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cordon'));
    }

    /**
     * Creates a form to delete a Cordon entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cordon_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }
}
