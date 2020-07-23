<?php

namespace Presis\GeneralBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\GeneralBundle\Entity\BultoExcedente;
use Presis\GeneralBundle\Form\BultoExcedenteType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * BultoExcedente controller.
 *
 */
class BultoExcedenteController extends Controller
{

    /**
     * Lists all BultoExcedente entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('PresisGeneralBundle:BultoExcedente')->findAll();

        return $this->render('PresisGeneralBundle:BultoExcedente:index.html.twig', array(
            'entities' => $query,
        ));

        /*$paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);*/

    }

    public function getBultosExcedentesAction()
    {

        $user=$this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();

        if ($user->hasRole('ROLE_VENDEDOR')){
            $query = $em->createQuery('SELECT be.id, be.bultoExcedente, be.costoBultoExcedente, c.empresa, s.descripcion, 
co.descripcion  AS cordonEntrega, cr.descripcion AS cordonRetiro, v.nombre
        FROM 
        PresisGeneralBundle:BultoExcedente be,
        PresisGeneralBundle:Cliente c,
        PresisServicioBundle:Servicio s,
        PresisServicioBundle:Cordon co,
        PresisGeneralBundle:Vendedor v,
        PresisServicioBundle:Cordon cr
        WHERE
        be.cliente=c AND 
        be.servicio= s AND 
        be.cordonEntrega=co AND
        be.cordonRetiro=cr AND 
        c.vendedor=v AND    
        c.vendedor = :vendedor 
        ORDER BY be.id ASC
        ');
            $query->setParameter('vendedor',$user->getVendedor());
        }else{
            $query = $em->createQuery('SELECT be.id, be.bultoExcedente, be.costoBultoExcedente, c.empresa, s.descripcion, 
co.descripcion  AS cordonEntrega, cr.descripcion AS cordonRetiro
        FROM 
        PresisGeneralBundle:BultoExcedente be,
        PresisGeneralBundle:Cliente c,
        PresisServicioBundle:Servicio s,
        PresisServicioBundle:Cordon co,
        PresisServicioBundle:Cordon cr
        WHERE
        be.cliente=c AND 
        be.servicio= s AND 
        be.cordonEntrega=co AND
        be.cordonRetiro=cr
        ORDER BY be.id ASC
        ');
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        //die($json);

        /*return $this->render('PresisGeneralBundle:BultoExcedente:index.html.twig', array(
            'entities' => $query,
        ));*/

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);

    }

    /**
     * Creates a new BultoExcedente entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BultoExcedente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bultoexcedente_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisGeneralBundle:BultoExcedente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BultoExcedente entity.
     *
     * @param BultoExcedente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BultoExcedente $entity)
    {

        $empresa = $this->container->getParameter('empresa');

        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new BultoExcedenteType($securityContext, $empresa), $entity, array(
            'action' => $this->generateUrl('bultoexcedente_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BultoExcedente entity.
     *
     */
    public function newAction()
    {
        $entity = new BultoExcedente();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisGeneralBundle:BultoExcedente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BultoExcedente entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:BultoExcedente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BultoExcedente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:BultoExcedente:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BultoExcedente entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:BultoExcedente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BultoExcedente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:BultoExcedente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BultoExcedente entity.
    *
    * @param BultoExcedente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BultoExcedente $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new BultoExcedenteType($securityContext), $entity, array(
            'action' => $this->generateUrl('bultoexcedente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BultoExcedente entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:BultoExcedente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BultoExcedente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bultoexcedente_edit', array('id' => $id)));
        }

        return $this->render('PresisGeneralBundle:BultoExcedente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BultoExcedente entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisGeneralBundle:BultoExcedente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BultoExcedente entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('bultoexcedente'));
    }

    /**
     * Creates a form to delete a BultoExcedente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bultoexcedente_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
