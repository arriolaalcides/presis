<?php

namespace Presis\GuiaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\GuiaBundle\Entity\Guia;
use Presis\GuiaBundle\Form\GuiaType;
use Symfony\Component\HttpFoundation\Response;
use Ps\PdfBundle\Annotation\Pdf;

/**
 * Guia controller.
 *
 */
class GuiaController extends Controller
{

    public function ajaxAction(){
        $em = $this->getDoctrine()->getManager();
        $user=$this->get('security.context')->getToken()->getUser();
        if ($user->hasRole('ROLE_CLIENTE')) {

            $query = $em->createQuery(
                'SELECT g.id,g.fechahora,c.empresa
                FROM PresisGuiaBundle:Guia g,PresisGeneralBundle:Cliente c
                where g.cliente=c
                and g.cliente=:cli');
            $query->setParameter('cli',$user->getCliente());

        }else{
            $query = $em->createQuery(
                'SELECT g.id,g.fechahora,c.empresa
                FROM PresisGuiaBundle:Guia g,PresisGeneralBundle:Cliente c
                where g.cliente=c');
        }

        $categorias = $query->getResult();

        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($categorias, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);


    }
    /**
     * Lists all Guia entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisGuiaBundle:Guia')->findAll();

        return $this->render('PresisGuiaBundle:Guia:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Guia entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Guia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisGuiaBundle:Guia:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Guia entity.
     *
     * @param Guia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Guia $entity)
    {
        $form = $this->createForm(new GuiaType(), $entity, array(
            'action' => $this->generateUrl('guia_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Guia entity.
     *
     */
    public function newAction()
    {
        $entity = new Guia();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisGuiaBundle:Guia:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Guia entity.
     * @Pdf()
     */
    public function showAction($id)
    {
        $format = $this->get('request')->get('_format');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGuiaBundle:Guia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Guia entity.');
        }

        return $this->render(sprintf('PresisGuiaBundle:Guia:guia.%s.twig', $format), array(
            'entity' => $entity,
            'attr'=>array('target'=>'_blank'),
        ));
    }

    /**
     * Displays a form to edit an existing Guia entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGuiaBundle:Guia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Guia entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGuiaBundle:Guia:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Guia entity.
    *
    * @param Guia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Guia $entity)
    {
        $form = $this->createForm(new GuiaType(), $entity, array(
            'action' => $this->generateUrl('guia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Guia entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGuiaBundle:Guia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Guia entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('guia_edit', array('id' => $id)));
        }

        return $this->render('PresisGuiaBundle:Guia:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Guia entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisGuiaBundle:Guia')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Guia entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('guia'));
    }

    /**
     * Creates a form to delete a Guia entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('guia_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
