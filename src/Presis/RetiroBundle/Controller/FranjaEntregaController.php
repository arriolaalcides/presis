<?php

namespace Presis\RetiroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\RetiroBundle\Entity\FranjaEntrega;
use Presis\RetiroBundle\Form\FranjaEntregaType;
use Symfony\Component\HttpFoundation\Response;


/**
 * FranjaEntrega controller.
 *
 */
class FranjaEntregaController extends Controller
{
    public function ajaxAction(){


        $em=$this->getDoctrine()->getManager();
        $categorias=$em->getRepository("PresisRetiroBundle:FranjaEntrega")->findAll();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($categorias, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);


    }

    /**
     * Lists all FranjaEntrega entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisRetiroBundle:FranjaEntrega')->findAll();

        return $this->render('PresisRetiroBundle:FranjaEntrega:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new FranjaEntrega entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new FranjaEntrega();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('franjaentrega_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisRetiroBundle:FranjaEntrega:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a FranjaEntrega entity.
     *
     * @param FranjaEntrega $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FranjaEntrega $entity)
    {
        $form = $this->createForm(new FranjaEntregaType(), $entity, array(
            'action' => $this->generateUrl('franjaentrega_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Franja','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new FranjaEntrega entity.
     *
     */
    public function newAction()
    {
        $entity = new FranjaEntrega();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisRetiroBundle:FranjaEntrega:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FranjaEntrega entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:FranjaEntrega')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FranjaEntrega entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:FranjaEntrega:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FranjaEntrega entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:FranjaEntrega')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FranjaEntrega entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:FranjaEntrega:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a FranjaEntrega entity.
    *
    * @param FranjaEntrega $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(FranjaEntrega $entity)
    {
        $form = $this->createForm(new FranjaEntregaType(), $entity, array(
            'action' => $this->generateUrl('franjaentrega_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing FranjaEntrega entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:FranjaEntrega')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FranjaEntrega entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('franjaentrega_edit', array('id' => $id)));
        }

        return $this->render('PresisRetiroBundle:FranjaEntrega:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a FranjaEntrega entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisRetiroBundle:FranjaEntrega')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FranjaEntrega entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('franjaentrega'));
    }

    /**
     * Creates a form to delete a FranjaEntrega entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('franjaentrega_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))->getForm()
        ;
    }
}
