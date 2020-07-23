<?php

namespace Presis\ServicioBundle\Controller;

use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Presis\ServicioBundle\Entity\Lista;
use Presis\ServicioBundle\Form\ListaType;

/**
 * Lista controller.
 *
 */
class ListaController extends Controller
{

    public function ajaxAction(){
        $em = $this->getDoctrine()->getManager();
        $listrepo=$em->getRepository("PresisServicioBundle:Lista");
        $user=$this->get('security.context')->getToken()->getUser();

        $listas = $listrepo->findTableData($user);
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($listas, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }
    /**
     * Lists all Lista entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisServicioBundle:Lista')->findAll();

        return $this->render('PresisServicioBundle:Lista:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    public function createAumentoForm(){

        $user=$this->container->get('security.context')->getToken()->getUser();
        if ($user->hasRole("ROLE_VENDEDOR")) {
            $form = $this->createFormBuilder()->add('lista', 'entity',
                array('class' => 'PresisServicioBundle:Lista',
                    'choices' => $user->getVendedor()->getListas()
                ))
                ->add('porcentaje', 'percent')
                ->add('aumento', 'submit', array('label' => 'Modificar','attr' => array('class'=> 'btn btn-success')))->getForm();

            return $form;
        }else {
            if ($user->hasRole("ROLE_ADMIN")) {
                $form = $this->createFormBuilder()->add('lista', 'entity',
                    array('class' => 'PresisServicioBundle:Lista'))
                    ->add('porcentaje', 'percent')
                    ->add('aumento', 'submit', array('label' => 'Modificar','attr' => array('class'=> 'btn btn-success')))->getForm();

                return $form;
            }else{
                throw $this->createAccessDeniedException('No posee permisos para acceder a esta pagina');

            }
        }
    }
    private function aumentarPrecio($lista,$porcentaje){
        $precios=$lista->getPrecios();
        $em=$this->getDoctrine()->getManager();
        foreach ($precios as $precio){
            $nuevoPrecio=$precio->getPrecio()+(($precio->getPrecio()*$porcentaje));
            $precio->setPrecio($nuevoPrecio);
            $em->persist($precio);

        }
        $em->flush();
    }
    public function aumentoAction(Request $request){
        $form=$this->createAumentoForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            // perform some action, such as saving the task to the database
            $lista=$form->getData()["lista"];
            $porce=$form->getData()["porcentaje"];
            $this->aumentarPrecio($lista,$porce);
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success("Lista de precio modificada correctamente");
           return $this->redirect($this->generateUrl("lista_aumento"));
            //return $this->render('PresisGeneralBundle:L:index.html.twig');
        }
        return $this->render('@PresisServicio/Lista/aumento.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     * Creates a new Lista entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Lista();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('lista_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisServicioBundle:Lista:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Lista entity.
     *
     * @param Lista $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Lista $entity)
    {
        $securityContext = $this->container->get('security.context');


        $form = $this->createForm(new ListaType($securityContext), $entity, array(
            'action' => $this->generateUrl('lista_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar Lista','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Lista entity.
     *
     */
    public function newAction()
    {
        $entity = new Lista();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisServicioBundle:Lista:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Lista entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Lista')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Lista entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:Lista:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Lista entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Lista')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Lista entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisServicioBundle:Lista:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Lista entity.
    *
    * @param Lista $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Lista $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new ListaType($securityContext), $entity, array(
            'action' => $this->generateUrl('lista_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar Cambios','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Lista entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisServicioBundle:Lista')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Lista entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('lista_edit', array('id' => $id)));
        }

        return $this->render('PresisServicioBundle:Lista:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Lista entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisServicioBundle:Lista')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Lista entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('lista'));
    }

    /**
     * Creates a form to delete a Lista entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lista_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }
}
