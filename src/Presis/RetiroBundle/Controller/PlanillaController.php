<?php

namespace Presis\RetiroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Presis\RetiroBundle\Entity\Planilla;
use Presis\RetiroBundle\Form\PlanillaType;

/**
 * Planilla controller.
 *
 */
class PlanillaController extends Controller
{

    /**
     * Lists all Planilla entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisRetiroBundle:Planilla')->findAll();

        return $this->render('PresisRetiroBundle:Planilla:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Planilla entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Planilla();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('planilla_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisRetiroBundle:Planilla:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Planilla entity.
     *
     * @param Planilla $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Planilla $entity)
    {
        $form = $this->createForm(new PlanillaType(), $entity, array(
            'action' => $this->generateUrl('planilla_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Planilla entity.
     *
     */
    public function newAction()
    {
        $entity = new Planilla();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisRetiroBundle:Planilla:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    public function ajaxAction(){

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p.id,p.fecha,p.numero,p.distribuidor,c.empresa
            FROM PresisRetiroBundle:Planilla p,PresisGeneralBundle:Cliente c
            where p.cliente=c');


        $categorias = $query->getResult();

        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($categorias, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);


    }
    /**
     * Finds and displays a Planilla entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Planilla')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Planilla entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:Planilla:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Planilla entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Planilla')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Planilla entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisRetiroBundle:Planilla:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Planilla entity.
    *
    * @param Planilla $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Planilla $entity)
    {
        $form = $this->createForm(new PlanillaType(), $entity, array(
            'action' => $this->generateUrl('planilla_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Planilla entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisRetiroBundle:Planilla')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Planilla entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('planilla_edit', array('id' => $id)));
        }

        return $this->render('PresisRetiroBundle:Planilla:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Planilla entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisRetiroBundle:Planilla')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Planilla entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('planilla'));
    }

    /**
     * Creates a form to delete a Planilla entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('planilla_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
