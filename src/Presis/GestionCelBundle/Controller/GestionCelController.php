<?php

namespace Presis\GestionCelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\GestionCelBundle\Entity\GestionCel;
use Presis\GestionCelBundle\Form\GestionCelType;

/**
 * GestionCel controller.
 *
 */
class GestionCelController extends Controller
{

    /**
     * Lists all GestionCel entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GestionCelBundle:GestionCel')->findAll();

        return $this->render('GestionCelBundle:GestionCel:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new GestionCel entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new GestionCel();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gestioncel_show', array('id' => $entity->getId())));
        }

        return $this->render('GestionCelBundle:GestionCel:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a GestionCel entity.
     *
     * @param GestionCel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(GestionCel $entity)
    {
        $form = $this->createForm(new GestionCelType(), $entity, array(
            'action' => $this->generateUrl('gestioncel_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new GestionCel entity.
     *
     */
    public function newAction()
    {
        $entity = new GestionCel();
        $form   = $this->createCreateForm($entity);

        return $this->render('GestionCelBundle:GestionCel:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a GestionCel entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GestionCelBundle:GestionCel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GestionCel entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('GestionCelBundle:GestionCel:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing GestionCel entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GestionCelBundle:GestionCel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GestionCel entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('GestionCelBundle:GestionCel:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a GestionCel entity.
    *
    * @param GestionCel $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(GestionCel $entity)
    {
        $form = $this->createForm(new GestionCelType(), $entity, array(
            'action' => $this->generateUrl('gestioncel_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing GestionCel entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GestionCelBundle:GestionCel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GestionCel entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gestioncel_edit', array('id' => $id)));
        }

        return $this->render('GestionCelBundle:GestionCel:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a GestionCel entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GestionCelBundle:GestionCel')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find GestionCel entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gestioncel'));
    }

    /**
     * Creates a form to delete a GestionCel entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gestioncel_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
