<?php

namespace Presis\ServicioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\ServicioBundle\Entity\Precio;
use Presis\ServicioBundle\Form\PrecioType;
use Symfony\Component\HttpFoundation\Response;
/**
 * Precio controller.
 *
 */
class PrecioController extends Controller
{
    public function ajaxAction(){
        $em = $this->getDoctrine()->getManager();
        $clierepo=$em->getRepository("PresisServicioBundle:Precio");
        $user=$this->get('security.context')->getToken()->getUser();

        $clientes = $clierepo->findTableData($user);
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($clientes, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }
    /**
     * Lists all Precio entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisServicioBundle:Precio')->findAll();

        return $this->render('PresisServicioBundle:Precio:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Precio entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Precio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('precio_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisServicioBundle:Precio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Precio entity.
     *
     * @param Precio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Precio $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new PrecioType($securityContext), $entity, array(
            'action' => $this->generateUrl('precio_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Precio','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Precio entity.
     *
     */
    public function newAction()
    {
        $entity = new Precio();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisServicioBundle:Precio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Precio entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Precio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Precio entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:Precio:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Precio entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Precio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Precio entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:Precio:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Precio entity.
    *
    * @param Precio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Precio $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new PrecioType($securityContext), $entity, array(
            'action' => $this->generateUrl('precio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar Cambios','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Precio entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Precio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Precio entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('precio_edit', array('id' => $id)));
        }

        return $this->render('PresisServicioBundle:Precio:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Precio entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisServicioBundle:Precio')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Precio entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('precio'));
    }

    /**
     * Creates a form to delete a Precio entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('precio_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }
}
