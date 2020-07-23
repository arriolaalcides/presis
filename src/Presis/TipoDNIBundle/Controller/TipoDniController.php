<?php

namespace Presis\TipoDNIBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Presis\TipoDNIBundle\Entity\TipoDni;
use Presis\TipoDNIBundle\Form\TipoDniType;

/**
 * TipoDni controller.
 *
 * @Route("/tipodni")
 */
class TipoDniController extends Controller
{

    /**
     * Lists all TipoDni entities.
     *
     * @Route("/", name="tipodni")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TipoDNIBundle:TipoDni')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new TipoDni entity.
     *
     * @Route("/", name="tipodni_create")
     * @Method("POST")
     * @Template("TipoDNIBundle:TipoDni:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TipoDni();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipodni_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a TipoDni entity.
     *
     * @param TipoDni $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoDni $entity)
    {
        $form = $this->createForm(new TipoDniType(), $entity, array(
            'action' => $this->generateUrl('tipodni_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoDni entity.
     *
     * @Route("/new", name="tipodni_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TipoDni();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a TipoDni entity.
     *
     * @Route("/{id}", name="tipodni_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TipoDNIBundle:TipoDni')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDni entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TipoDni entity.
     *
     * @Route("/{id}/edit", name="tipodni_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TipoDNIBundle:TipoDni')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDni entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a TipoDni entity.
    *
    * @param TipoDni $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoDni $entity)
    {
        $form = $this->createForm(new TipoDniType(), $entity, array(
            'action' => $this->generateUrl('tipodni_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoDni entity.
     *
     * @Route("/{id}", name="tipodni_update")
     * @Method("PUT")
     * @Template("TipoDNIBundle:TipoDni:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TipoDNIBundle:TipoDni')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDni entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipodni_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a TipoDni entity.
     *
     * @Route("/{id}", name="tipodni_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TipoDNIBundle:TipoDni')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TipoDni entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tipodni'));
    }

    /**
     * Creates a form to delete a TipoDni entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipodni_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
