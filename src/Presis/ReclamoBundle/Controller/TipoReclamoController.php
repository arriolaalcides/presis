<?php

namespace Presis\ReclamoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\ReclamoBundle\Entity\TipoReclamo;
use Presis\ReclamoBundle\Form\TipoReclamoType;

/**
 * TipoReclamo controller.
 *
 */
class TipoReclamoController extends Controller
{

    /**
     * Lists all TipoReclamo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ReclamoBundle:TipoReclamo')->findAll();

        return $this->render('ReclamoBundle:TipoReclamo:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TipoReclamo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoReclamo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tiporeclamo_show', array('id' => $entity->getId())));
        }

        return $this->render('ReclamoBundle:TipoReclamo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoReclamo entity.
     *
     * @param TipoReclamo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoReclamo $entity)
    {
        $form = $this->createForm(new TipoReclamoType(), $entity, array(
            'action' => $this->generateUrl('tiporeclamo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoReclamo entity.
     *
     */
    public function newAction()
    {
        $entity = new TipoReclamo();
        $form   = $this->createCreateForm($entity);

        return $this->render('ReclamoBundle:TipoReclamo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TipoReclamo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ReclamoBundle:TipoReclamo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoReclamo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ReclamoBundle:TipoReclamo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TipoReclamo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ReclamoBundle:TipoReclamo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoReclamo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ReclamoBundle:TipoReclamo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TipoReclamo entity.
    *
    * @param TipoReclamo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoReclamo $entity)
    {
        $form = $this->createForm(new TipoReclamoType(), $entity, array(
            'action' => $this->generateUrl('tiporeclamo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoReclamo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ReclamoBundle:TipoReclamo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoReclamo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tiporeclamo_edit', array('id' => $id)));
        }

        return $this->render('ReclamoBundle:TipoReclamo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a TipoReclamo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ReclamoBundle:TipoReclamo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TipoReclamo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tiporeclamo'));
    }

    /**
     * Creates a form to delete a TipoReclamo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tiporeclamo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
