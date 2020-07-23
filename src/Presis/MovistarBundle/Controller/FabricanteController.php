<?php

namespace Presis\MovistarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Presis\MovistarBundle\Entity\Fabricante;
use Presis\MovistarBundle\Entity\FabricanteRepository;
use Presis\MovistarBundle\Form\FabricanteType;

use Presis\MovistarBundle\Entity\Modelo;
use Presis\MovistarBundle\Entity\ModeloRepository;

use Symfony\Component\HttpFoundation\Response;

class FabricanteController extends Controller
{
    public function ajaxAction()
    {
    $em=$this->getDoctrine()->getManager();
    $consulta = $em->createQueryBuilder()
        ->select("f.id, f.descricion")
        ->from('MovistarBundle:Fabricante', 'f');

        $consulta=$consulta->getQuery();
        $fabricante=$consulta->getResult();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($fabricante, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MovistarBundle:Fabricante')->findAll();

        return $this->render('MovistarBundle:Fabricante:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Creates a new Fabricante entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Fabricante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fabricante_show', array('id' => $entity->getId())));
        }

        return $this->render('MovistarBundle:Fabricante:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Fabricante entity.
     *
     * @param Fabricante $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Fabricante $entity)
    {
        $form = $this->createForm(new FabricanteType(), $entity, array(
            'action' => $this->generateUrl('fabricante_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Fabricante','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Fabricante entity.
     *
     */
    public function newAction()
    {
        $entity = new Fabricante();
        $form   = $this->createCreateForm($entity);

        return $this->render('MovistarBundle:Fabricante:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Fabricante entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MovistarBundle:Fabricante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fabricante entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MovistarBundle:Fabricante:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Fabricante entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MovistarBundle:Fabricante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fabricante entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MovistarBundle:Fabricante:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Fabricante entity.
    *
    * @param Fabricante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Fabricante $entity)
    {
        $form = $this->createForm(new FabricanteType(), $entity, array(
            'action' => $this->generateUrl('fabricante_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Grabar','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Fabricante entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MovistarBundle:Fabricante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fabricante entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('fabricante_edit', array('id' => $id)));
        }

        return $this->render('MovistarBundle:Fabricante:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Fabricante entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MovistarBundle:Fabricante')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Fabricante entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('fabricante'));
    }

    /**
     * Creates a form to delete a Fabricante entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fabricante_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }

    
}
