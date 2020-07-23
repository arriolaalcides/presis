<?php

namespace Presis\GeneralBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\GeneralBundle\Entity\Vendedor;
use Presis\GeneralBundle\Form\VendedorType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendedor controller.
 *
 */
class VendedorController extends Controller
{
    public function ajaxAction(){

        $em = $this->getDoctrine()->getManager();
        $user=$this->get('security.context')->getToken()->getUser();

            $query = $em->createQuery(
                'SELECT v.id,v.nombre,v.telefono,v.direccion,v.cp
                FROM PresisGeneralBundle:Vendedor v'
            );



        $vendedores = $query->getResult();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($vendedores, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }
    /**
     * Lists all Vendedor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisGeneralBundle:Vendedor')->findAll();

        return $this->render('PresisGeneralBundle:Vendedor:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Vendedor entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Vendedor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('vendedor_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisGeneralBundle:Vendedor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Vendedor entity.
     *
     * @param Vendedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Vendedor $entity)
    {
        $form = $this->createForm(new VendedorType(), $entity, array(
            'action' => $this->generateUrl('vendedor_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Comercial','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Vendedor entity.
     *
     */
    public function newAction()
    {
        $entity = new Vendedor();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisGeneralBundle:Vendedor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Vendedor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Vendedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendedor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:Vendedor:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Vendedor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Vendedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendedor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:Vendedor:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Vendedor entity.
    *
    * @param Vendedor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Vendedor $entity)
    {
        $form = $this->createForm(new VendedorType(), $entity, array(
            'action' => $this->generateUrl('vendedor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Grabar','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Vendedor entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Vendedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendedor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('vendedor_edit', array('id' => $id)));
        }

        return $this->render('PresisGeneralBundle:Vendedor:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Vendedor entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisGeneralBundle:Vendedor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Vendedor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('vendedor'));
    }

    /**
     * Creates a form to delete a Vendedor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vendedor_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }
}
