<?php

namespace Presis\GeneralBundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Presis\GeneralBundle\Entity\Cliente;
use Presis\GeneralBundle\Form\ClienteType;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Cliente controller.
 *
 */
class ClienteController extends Controller
{
    private function ajax() {
        $em = $this->getDoctrine()->getManager();
        $clierepo=$em->getRepository("PresisGeneralBundle:Cliente");
        $user=$this->get('security.context')->getToken()->getUser();
        //die($user);
        $clientes = $clierepo->findTableData($user);
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($clientes, "json");

        return $json;
    }

    public function ajaxAction(){
        $json = $this->ajax();

        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }
    
    public function ajax4selectAction() {
        return new Response($this->ajax());
    }
    
    /**
     * Lists all Cliente entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisGeneralBundle:Cliente')->findAll();

        return $this->render('PresisGeneralBundle:Cliente:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Cliente entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Cliente();


        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $user=$this->get('security.context')->getToken()->getUser();
            if ($user->hasRole('ROLE_VENDEDOR')){
                $entity->setVendedor($user->getVendedor());
            }


            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cliente_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisGeneralBundle:Cliente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function searchAction(){

    }
    private function createCreateForm(Cliente $entity)
    {
        $securityContext = $this->container->get('security.context');
        $form = $this->createForm(new ClienteType($securityContext,null), $entity, array(
            'action' => $this->generateUrl('cliente_create'),
            'method' => 'POST',
        ));


        $form->add('submit', 'submit', array('label' => 'Agregar Cliente','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Cliente entity.
     *
     */
    public function newAction()
    {
        // $userManager=new UserManager();



        $entity = new Cliente();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisGeneralBundle:Cliente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Cliente entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:Cliente:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    //BUSCA LOS DATOS PARA COMPLETAR LAS FORMULAS
    public function findAction($id)
    {

        $entity = new Cliente();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        return new JsonResponse(array(
            'aforo' => $entity->getAforo(),
            'nroCta' => $entity->getNroCta(),
            'contrareembolsoEfectivo' => $entity->getContrareembolsoEfectivo(),
            'contrareembolsoCheque' => $entity->getContrareembolsoCheque(),
            'formaPago' => $entity->getFormaPagoNombre(),
            'is_porcentaje' => $entity->getIsPorcentaje(),
            'monto_servicio' => $entity->getMontoServicio(),
            'monto_guia_web' => $entity->getMontoGuiaWeb(),
            'tipoFacturacion' => $entity->getTipoFacturacion(),
            'tipoDeCobro' => $entity->getTipoDeCobro(),
        ));
    }


    /**
     * Displays a form to edit an existing Cliente entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Cliente')->find($id);
        $user=$this->get('security.context')->getToken()->getUser();
        if ($user->hasRole("ROLE_VENDEDOR")) {
            if (!($entity->getVendedor() == $user->getVendedor())) {
                throw $this->createAccessDeniedException('No posee permisos para acceder a esta pagina');

            }
        }
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:Cliente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Cliente $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new ClienteType($securityContext,$entity->getId()), $entity, array(
            'action' => $this->generateUrl('cliente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar Cambios','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Cliente entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $data=$editForm->getData();

            $lista = $em->getRepository('PresisServicioBundle:Lista')->findOneBy(array('cliente'=>$entity->getId()));
            if($lista){
                if($lista->getDescripcion()!=$data->getLista()) {
                    $lista->setCliente(null);
                }
            }


            //die($data->getLista());
            //var_dump($data);

            $user=$this->get('security.context')->getToken()->getUser();
            if ($user->hasRole('ROLE_VENDEDOR')){
                $entity->setVendedor($user->getVendedor());
            }
            $em->flush();

            return $this->redirect($this->generateUrl('cliente_edit', array('id' => $id)));
        }

        return $this->render('PresisGeneralBundle:Cliente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Cliente entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisGeneralBundle:Cliente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cliente entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cliente'));
    }

    /**
     * Creates a form to delete a Cliente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cliente_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
            ;
    }
}
