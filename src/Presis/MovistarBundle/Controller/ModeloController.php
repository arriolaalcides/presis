<?php

namespace Presis\MovistarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Presis\MovistarBundle\Entity\Fabricante;
use Presis\MovistarBundle\Entity\FabricanteRepository;

use Presis\MovistarBundle\Entity\Modelo;
use Presis\MovistarBundle\Entity\ModeloRepository;
use Presis\MovistarBundle\Form\ModeloType;

use Symfony\Component\HttpFoundation\Response;

class ModeloController extends Controller
{
    
    public function ajaxAction()
    {
    $em=$this->getDoctrine()->getManager();
    $consulta = $em->createQueryBuilder()
        ->select("m.id, m.descripcion, m.valorDeclarado, m.activo, f.descricion")
        ->from('MovistarBundle:Modelo', 'm')
        ->leftJoin('m.fabricante', 'f')
        ->where('m.activo = 1');
            
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

        $entities = $em->getRepository('MovistarBundle:Modelo')->findAll();

        return $this->render('MovistarBundle:Modelo:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Creates a new Modelo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Modelo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setActivo(1);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('modelo_show', array('id' => $entity->getId())));
        }

        return $this->render('MovistarBundle:Modelo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Modelo entity.
     *
     * @param Modelo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Modelo $entity)
    {
        $form = $this->createForm(new ModeloType(), $entity, array(
            'action' => $this->generateUrl('modelo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Modelo','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Modelo entity.
     *
     */
    public function newAction()
    {
        $entity = new Modelo();
        $form   = $this->createCreateForm($entity);

        return $this->render('MovistarBundle:Modelo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Modelo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MovistarBundle:Modelo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Modelo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MovistarBundle:Modelo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Modelo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MovistarBundle:Modelo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Modelo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MovistarBundle:Modelo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Modelo entity.
    *
    * @param Fabricante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Modelo $entity)
    {
        $form = $this->createForm(new ModeloType(), $entity, array(
            'action' => $this->generateUrl('modelo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Grabar','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Modelo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MovistarBundle:Modelo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Modelo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('modelo_edit', array('id' => $id)));
        }

        return $this->render('MovistarBundle:Modelo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Modelo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MovistarBundle:Modelo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Modelo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('modelo'));
    }

    /**
     * Creates a form to delete a Modelo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modelo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }
}
