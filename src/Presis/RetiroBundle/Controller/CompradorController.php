<?php

namespace Presis\RetiroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\RetiroBundle\Entity\Comprador;
use Presis\RetiroBundle\Form\CompradorType;

/**
 * Comprador controller.
 *
 */
class CompradorController extends Controller
{

    /**
     * Lists all Comprador entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisRetiroBundle:Comprador')->findAll();

        return $this->render('PresisRetiroBundle:Comprador:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Comprador entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Comprador();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('comprador_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisRetiroBundle:Comprador:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Comprador entity.
     *
     * @param Comprador $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Comprador $entity)
    {
        $form = $this->createForm(new CompradorType(), $entity, array(
            'action' => $this->generateUrl('comprador_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Comprador entity.
     *
     */
    public function newAction()
    {
        $entity = new Comprador();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisRetiroBundle:Comprador:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Comprador entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Comprador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comprador entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:Comprador:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Comprador entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Comprador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comprador entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:Comprador:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Comprador entity.
    *
    * @param Comprador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Comprador $entity)
    {
        $form = $this->createForm(new CompradorType(), $entity, array(
            'action' => $this->generateUrl('comprador_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Comprador entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Comprador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comprador entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('comprador_edit', array('id' => $id)));
        }

        return $this->render('PresisRetiroBundle:Comprador:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Comprador entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisRetiroBundle:Comprador')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Comprador entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('comprador'));
    }

    /**
     * Creates a form to delete a Comprador entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comprador_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
