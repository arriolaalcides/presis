<?php

namespace Presis\ServicioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Presis\ServicioBundle\Entity\CpCordon;
use Presis\ServicioBundle\Form\CpCordonType;

/**
 * CpCordon controller.
 *
 */
class CpCordonController extends Controller
{
    public function ajaxAction(){

        $em=$this->getDoctrine()->getManager();
        $consulta = $em->createQueryBuilder()
            ->from('PresisServicioBundle:CpCordon', 'cp')
            ->from('PresisServicioBundle:Cordon','c')
            ->select("cp.id,c.descripcion cordon,cp.cp,cp.partido,cp.barrio,cp.localidad,cp.zona,cp.subzona,cp.provincia,cp.prestador,cp.tiempoDeEntrega,cp.tipoServicio")
            ->where("cp.cordon=c");


        $consulta=$consulta->getQuery();
        $cpcordones=$consulta->getResult();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($cpcordones, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }
    /**
     * Lists all CpCordon entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisServicioBundle:CpCordon')->findAll();

        return $this->render('PresisServicioBundle:CpCordon:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new CpCordon entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CpCordon();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cpcordon_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisServicioBundle:CpCordon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CpCordon entity.
     *
     * @param CpCordon $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CpCordon $entity)
    {
        $form = $this->createForm(new CpCordonType(), $entity, array(
            'action' => $this->generateUrl('cpcordon_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar CP Cordón','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new CpCordon entity.
     *
     */
    public function newAction()
    {
        $entity = new CpCordon();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisServicioBundle:CpCordon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CpCordon entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:CpCordon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CpCordon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:CpCordon:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CpCordon entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:CpCordon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CpCordon entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:CpCordon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CpCordon entity.
    *
    * @param CpCordon $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CpCordon $entity)
    {
        $form = $this->createForm(new CpCordonType(), $entity, array(
            'action' => $this->generateUrl('cpcordon_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Grabar','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing CpCordon entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:CpCordon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CpCordon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cpcordon_edit', array('id' => $id)));
        }

        return $this->render('PresisServicioBundle:CpCordon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CpCordon entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisServicioBundle:CpCordon')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CpCordon entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cpcordon'));
    }

    /**
     * Creates a form to delete a CpCordon entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cpcordon_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }
}
