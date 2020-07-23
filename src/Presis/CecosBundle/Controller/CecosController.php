<?php

namespace Presis\CecosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\CecosBundle\Entity\Cecos;
use Presis\CecosBundle\Form\CecosType;
use Symfony\Component\HttpFoundation\Response;
/**
 * Cecos controller.
 *
 */
class CecosController extends Controller
{

    /**
     * Lists all Cecos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CecosBundle:Cecos')->findAll();

        return $this->render('CecosBundle:Cecos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Cecos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Cecos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('cecos_show', array('id' => $entity->getId())));
            return $this->render('CecosBundle:Cecos:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));
        }

        return $this->render('CecosBundle:Cecos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Cecos entity.
     *
     * @param Cecos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cecos $entity)
    {
        $form = $this->createForm(new CecosType(), $entity, array(
            'action' => $this->generateUrl('cecos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Cecos entity.
     *
     */
    public function newAction()
    {
        $entity = new Cecos();
        $form   = $this->createCreateForm($entity);

        return $this->render('CecosBundle:Cecos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Cecos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CecosBundle:Cecos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cecos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CecosBundle:Cecos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Cecos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CecosBundle:Cecos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cecos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CecosBundle:Cecos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Cecos entity.
    *
    * @param Cecos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Cecos $entity)
    {
        $form = $this->createForm(new CecosType(), $entity, array(
            'action' => $this->generateUrl('cecos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Cecos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CecosBundle:Cecos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cecos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cecos_edit', array('id' => $id)));
        }

        return $this->render('CecosBundle:Cecos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Cecos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CecosBundle:Cecos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cecos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cecos'));
    }

    /**
     * Creates a form to delete a Cecos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cecos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function selectAction(Request $request){

        $cliente_id = $request->get("cliente_id");

        $em = $this->getDoctrine()->getManager();
        $q = $em->getRepository("CecosBundle:Cecos")->findCecoByCliente($cliente_id);
        $cecos = $q->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize($cecos, 'json');
        return new Response($data);

    }
}
