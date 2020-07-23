<?php

namespace Presis\ExpresoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\ExpresoBundle\Entity\Expreso;
use Presis\ExpresoBundle\Form\ExpresoType;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Expreso controller.
 *
 */
class ExpresoController extends Controller
{
    public function ajax()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT e.id, e.nombre 
          FROM ExpresoBundle:Expreso e
            ORDER BY e.nombre ASC');

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return $json;
    }

    /*private function ajax() {
        $em = $this->getDoctrine()->getManager();
        $expresos = $em->getRepository('ExpresoBundle:Expreso')->findAll();

        $serializer = $this->get('jms_serializer');
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $json=$serializer->serialize($expresos, "json", $context);

        return $json;
    }*/

    public function asajaxAction()
    {
        $json=$this->ajax();

        return new Response('{"data":' . $json . '}');
    }

    public function ajax4selectAction() {
        return new Response($this->ajax());
    }

    /**
     * Lists all Expreso entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ExpresoBundle:Expreso')->findAll();

        return $this->render('ExpresoBundle:Expreso:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Expreso entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Expreso();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('expreso'));
        }

        return $this->render('ExpresoBundle:Expreso:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Expreso entity.
     *
     * @param Expreso $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Expreso $entity)
    {
        $form = $this->createForm(new ExpresoType(), $entity, array(
            'action' => $this->generateUrl('expreso_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Expreso entity.
     *
     */
    public function newAction()
    {
        $entity = new Expreso();
        $form   = $this->createCreateForm($entity);

        return $this->render('ExpresoBundle:Expreso:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Expreso entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ExpresoBundle:Expreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expreso entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ExpresoBundle:Expreso:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Expreso entity.
    *
    * @param Expreso $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Expreso $entity)
    {
        $form = $this->createForm(new ExpresoType(), $entity, array(
            'action' => $this->generateUrl('expreso_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Expreso entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ExpresoBundle:Expreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expreso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('expreso'));
        }

        return $this->render('ExpresoBundle:Expreso:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Expreso entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ExpresoBundle:Expreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expreso entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('expreso'));
    }

    /**
     * Creates a form to delete a Expreso entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('expreso_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
