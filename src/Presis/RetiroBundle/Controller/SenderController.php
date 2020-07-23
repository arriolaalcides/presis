<?php

namespace Presis\RetiroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\RetiroBundle\Entity\Sender;
use Presis\RetiroBundle\Form\SenderType;

/**
 * Sender controller.
 *
 */
class SenderController extends Controller
{

    /**
     * Lists all Sender entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisRetiroBundle:Sender')->findAll();

        return $this->render('PresisRetiroBundle:Sender:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Sender entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Sender();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sender_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisRetiroBundle:Sender:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Sender entity.
     *
     * @param Sender $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Sender $entity)
    {
        $form = $this->createForm(new SenderType(), $entity, array(
            'action' => $this->generateUrl('sender_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Sender entity.
     *
     */
    public function newAction()
    {
        $entity = new Sender();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisRetiroBundle:Sender:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Sender entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Sender')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sender entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:Sender:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Sender entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Sender')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sender entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:Sender:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Sender entity.
    *
    * @param Sender $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Sender $entity)
    {
        $form = $this->createForm(new SenderType(), $entity, array(
            'action' => $this->generateUrl('sender_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Sender entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Sender')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sender entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('sender_edit', array('id' => $id)));
        }

        return $this->render('PresisRetiroBundle:Sender:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Sender entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisRetiroBundle:Sender')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sender entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sender'));
    }

    /**
     * Creates a form to delete a Sender entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sender_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
