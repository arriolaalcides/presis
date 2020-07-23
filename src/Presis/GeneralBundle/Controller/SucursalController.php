<?php

namespace Presis\GeneralBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Presis\GeneralBundle\Entity\Sucursal;
use Presis\GeneralBundle\Form\SucursalType;
use Doctrine\ORM\Tools\Pagination\Paginator;
/**
 * Sucursal controller.
 *
 */
class SucursalController extends Controller
{

    public function ajax4selectAction() {
        return new Response($this->ajax());
    }

    public function findSucursalAction()
    {

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $entity = new Sucursal();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Sucursal')->find($user->getSucursal()->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sucursal entity.');
        }

        return new JsonResponse(array(
            'codigo' => $entity->getCodSuc(),
            'empresa' => $entity->getRazonSocial(),
            'remitente' => $entity->getContacto(),
            'calle' => $entity->getCalle(),
            'altura' => $entity->getAltura(),
            'piso' => $entity->getPiso(),
            'dpto' => $entity->getDpto(),
            'localidad' => $entity->getLocalidad(),
            'provincia' => $entity->getProvincia(),
            'cp' => $entity->getCp(),
            'email' => $entity->getMail(),
            'celular' => $entity->getCelular(),
        ));
    }

    public function findSucursalGalanderAction()
    {

        $entity = new Sucursal();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Sucursal')->find('1081');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sucursal entity.');
        }

        return new JsonResponse(array(
            'codigo' => $entity->getCodSuc(),
            'empresa' => $entity->getRazonSocial(),
            'remitente' => $entity->getContacto(),
            'calle' => $entity->getCalle(),
            'altura' => $entity->getAltura(),
            'piso' => $entity->getPiso(),
            'dpto' => $entity->getDpto(),
            'localidad' => $entity->getLocalidad(),
            'provincia' => $entity->getProvincia(),
            'cp' => $entity->getCp(),
            'email' => $entity->getMail(),
            'celular' => $entity->getCelular(),
        ));

    }

    public function ajax()
    {
        $em = $this->getDoctrine()->getManager();

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if(!$user->hasRole('ROLE_OPERATIVO')){
            $query = $em->createQuery('SELECT s.id, s.descripcion 
          FROM PresisGeneralBundle:Sucursal s 
          WHERE s.esPropia=TRUE
          ORDER BY s.descripcion ASC');
        }
        if($user->hasRole('ROLE_OPERATIVO')){
            $query = $em->createQuery('SELECT s.id, s.descripcion 
          FROM PresisGeneralBundle:Sucursal s 
          WHERE s.descripcion=:descripcion
          AND s.esPropia=TRUE
          ORDER BY s.descripcion ASC');

            $query->setParameter('descripcion',$user->getSucursal()->getDescripcion());
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return $json;
    }

    public function selectAction(Request $request){

        $cliente_id = $request->get("cliente_id");

        $em = $this->getDoctrine()->getManager();
        $q = $em->getRepository("PresisGeneralBundle:Sucursal")->findSucursalByCliente($cliente_id);
        $sucursales = $q->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize($sucursales, 'json');
        return new Response($data);
    }

    public function showJson1Action($id)
    {

        $entity = new Sucursal();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Sucursal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sucursal entity.');
        }
        //die("hola");
        return new JsonResponse(array(
            'calle' => $entity->getCalle(),
            'altura' => $entity->getAltura(),
            'piso' => $entity->getPiso(),
            'depto' => $entity->getDpto(),
            'localidad' => $entity->getLocalidad(),
            'provincia' => $entity->getProvincia(),
            'cp' => $entity->getCp(),
            'contacto' => $entity->getContacto(),
            'telefono' => $entity->getCelular(),
            'mail' => $entity->getMail()
        ));

    }
    /**
     * Lists all Sucursal entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('PresisGeneralBundle:Sucursal')->findAll();

        return $this->render('PresisGeneralBundle:Sucursal:index.html.twig');
    }
    public function ajaxAction(){

        $em = $this->getDoctrine()->getManager();
        $user=$this->get('security.context')->getToken()->getUser();
        if ($user->hasRole('ROLE_VENDEDOR')){
            $query = $em->createQuery(
                'SELECT s.esPropia, s.id,s.codSuc,s.descripcion,s.calle,s.altura,s.piso,s.dpto,s.otherInfo,s.cp,c.empresa, s.localidad, s.provincia
                FROM PresisGeneralBundle:Sucursal s
                JOIN s.cliente c
                WHERE c.vendedor=:vendedor'
            );
            $query->setParameter('vendedor',$user->getVendedor());
        }else{
            $query = $em->createQuery(
                'SELECT s.esPropia, s.id,s.codSuc,s.descripcion,s.calle,s.altura,s.piso,s.dpto,s.otherInfo,s.cp,c.empresa, s.localidad, s.provincia
                FROM PresisGeneralBundle:Sucursal s
                JOIN s.cliente c'
            );
        }


        $sucursales = $query->getResult();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($sucursales, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }
    /**
     * Creates a new Sucursal entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Sucursal();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sucursal_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisGeneralBundle:Sucursal:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Sucursal entity.
     *
     * @param Sucursal $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Sucursal $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new SucursalType($securityContext), $entity, array(
            'action' => $this->generateUrl('sucursal_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Sucursal','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new Sucursal entity.
     *
     */
    public function newAction()
    {
        $entity = new Sucursal();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisGeneralBundle:Sucursal:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Sucursal entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Sucursal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sucursal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:Sucursal:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Sucursal entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Sucursal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sucursal entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisGeneralBundle:Sucursal:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Sucursal entity.
     *
     * @param Sucursal $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sucursal $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new SucursalType($securityContext), $entity, array(
            'action' => $this->generateUrl('sucursal_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar Cambios','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Sucursal entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisGeneralBundle:Sucursal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sucursal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('sucursal_edit', array('id' => $id)));
        }

        return $this->render('PresisGeneralBundle:Sucursal:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Sucursal entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisGeneralBundle:Sucursal')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sucursal entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sucursal'));
    }

    /**
     * Creates a form to delete a Sucursal entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sucursal_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
            ;
    }
}
