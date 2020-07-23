<?php

namespace Presis\ReclamoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\ReclamoBundle\Entity\Reclamo;
use Presis\ReclamoBundle\Form\ReclamoType;
use Symfony\Component\HttpFoundation\Response;
use Presis\RetiroBundle\Entity\Retiro;

/**
 * Reclamo controller.
 *
 */
class ReclamoController extends Controller
{
    /**
     * Devuelve todos los reclamos para un tracking especifico.
     *
     */
    public function asajaxAction($id_retiro)
    {
        $em = $this->getDoctrine()->getManager();

        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($id_retiro);

        if (!$retiro) {
            throw $this->createNotFoundException('Unable to find Reclamo entity.');
        }

        $entities = $retiro->getReclamos();

        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($entities, "json");

        return new Response('{"data":' . $json . '}');
    }

    /**
     * Lists all Reclamo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ReclamoBundle:Reclamo')->findAll();

        return $this->render('ReclamoBundle:Reclamo:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Reclamo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Reclamo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('reclamo_new', array('retiro_id' => $entity->getRetiro()->getId())));
        }

        return $this->render('ReclamoBundle:Reclamo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'comprador' => '',
            'comprador_direccion'   => ''
        ));
    }

    /**
     * Creates a form to create a Reclamo entity.
     *
     * @param Reclamo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Reclamo $entity)
    {
        $form = $this->createForm(new ReclamoType(), $entity, array(
            'action' => $this->generateUrl('reclamo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Reclamo entity.
     *
     */
    public function newAction($retiro_id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $retiro = $em->getRepository('PresisRetiroBundle:Retiro')->find($retiro_id);

        $entity = new Reclamo();
        $entity->setRetiro($retiro);
        $entity->setUserResolvio($this->container->get('security.context')->getToken()->getUser());
        $form   = $this->createCreateForm($entity);

        return $this->render('ReclamoBundle:Reclamo:new.html.twig', array(
            'entity'                => $entity,
            'form'                  => $form->createView(),
            'comprador'             => $retiro->getNombreComprador(),
            'comprador_direccion'   => $retiro->getDireccionComprador(),
        ));
    }

    /**
     * Finds and displays a Reclamo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ReclamoBundle:Reclamo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reclamo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ReclamoBundle:Reclamo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Reclamo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ReclamoBundle:Reclamo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reclamo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ReclamoBundle:Reclamo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Reclamo entity.
    *
    * @param Reclamo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Reclamo $entity)
    {
        $form = $this->createForm(new ReclamoType(), $entity, array(
            'action' => $this->generateUrl('reclamo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Reclamo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ReclamoBundle:Reclamo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reclamo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('reclamo_edit', array('id' => $id)));
        }

        return $this->render('ReclamoBundle:Reclamo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Reclamo entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ReclamoBundle:Reclamo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reclamo entity.');
        }

        $em->remove($entity);
        $em->flush();

        return new Response("Reclamo record $id deleted succesfully", Response::HTTP_OK);
    }

    /**
     * Creates a form to delete a Reclamo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reclamo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
