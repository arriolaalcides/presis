<?php

namespace Presis\FotoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\FotoBundle\Entity\Fotos;
use Presis\FotoBundle\Form\FotosType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;
/**
 * Fotos controller.
 *
 */
class FotosController extends Controller
{

    public function asAjaxAction($tracking)
    {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository("FotoBundle:Fotos")->createQueryBuilder('r')
            ->where('r.tracking = :tracking')
            ->setParameter('tracking', $tracking);

        $result = $entities->getQuery()
            ->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    /**
     * Lists all Fotos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FotoBundle:Fotos')->findAll();

        return $this->render('FotoBundle:Fotos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Fotos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Fotos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fotos_show', array('id' => $entity->getId())));
        }

        return $this->render('FotoBundle:Fotos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Fotos entity.
     *
     * @param Fotos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Fotos $entity)
    {
        $form = $this->createForm(new FotosType(), $entity, array(
            'action' => $this->generateUrl('fotos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Fotos entity.
     *
     */
    public function newAction()
    {
        $entity = new Fotos();
        $form   = $this->createCreateForm($entity);

        return $this->render('FotoBundle:Fotos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Fotos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FotoBundle:Fotos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fotos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FotoBundle:Fotos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Fotos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FotoBundle:Fotos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fotos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FotoBundle:Fotos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Fotos entity.
    *
    * @param Fotos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Fotos $entity)
    {
        $form = $this->createForm(new FotosType(), $entity, array(
            'action' => $this->generateUrl('fotos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Fotos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FotoBundle:Fotos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fotos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('fotos_edit', array('id' => $id)));
        }

        return $this->render('FotoBundle:Fotos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Fotos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FotoBundle:Fotos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Fotos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('fotos'));
    }

    /**
     * Creates a form to delete a Fotos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fotos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
