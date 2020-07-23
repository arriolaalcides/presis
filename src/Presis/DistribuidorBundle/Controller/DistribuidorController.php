<?php

namespace Presis\DistribuidorBundle\Controller;

use Presis\DistribuidorBundle\Entity\Presentismo;
use Presis\EstadoBundle\Entity\Estado;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\DistribuidorBundle\Entity\Distribuidor;
use Presis\DistribuidorBundle\Form\DistribuidorType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Distribuidor controller.
 *
 */
class DistribuidorController extends Controller
{

    public function listaPresentismoAction()
    {
        $fecha = new \DateTime();

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT d.id, CONCAT(COALESCE(d.nombre,\'\'), COALESCE(d.apellido,\'\')) AS apenom, d.codigo 
          FROM 
          DistribuidorBundle:Distribuidor d
          WHERE
          d.codigo NOT IN (SELECT pre.codigo from DistribuidorBundle:Presentismo pre WHERE pre.fecha=:fecha )
            ORDER BY apenom ASC');
        $query->setParameter("fecha", $fecha->format('Y-m-d'));

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);
    }

    public function ajax()
    {
        $em = $this->getDoctrine()->getManager();

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if(!$user->hasRole('ROLE_SUCURSAL')){
            $query = $em->createQuery('SELECT d.id, CONCAT(d.apellido,\' \',d.nombre) AS apenom, d.codigo 
          FROM 
          DistribuidorBundle:Distribuidor d
          ORDER BY apenom ASC');
        }
            if($user->hasRole('ROLE_SUCURSAL')){
            $query = $em->createQuery('SELECT d.id, CONCAT(d.apellido,\' \',d.nombre) AS apenom, d.codigo 
            FROM 
            DistribuidorBundle:Distribuidor d,
            PresisGeneralBundle:Sucursal s 
            WHERE d.sucursal=s
            AND d.sucursal=:sucursal
            ORDER BY apenom ASC');
            $query->setParameter('sucursal',$user->getSucursal());
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return $json;
    }

    public function ajax2Action()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT d.id, CONCAT(COALESCE(d.nombre,\'\'), COALESCE(d.apellido,\'\')) AS apenom 
          FROM DistribuidorBundle:Distribuidor d
            ORDER BY apenom ASC');

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return $json;
    }


    /*private function ajaxViejo() {
        $em = $this->getDoctrine()->getManager();
        $distribuidores = $em->getRepository('DistribuidorBundle:Distribuidor')->findAll();

        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($distribuidores, "json");

        return $json;
    }*/

    public function asajaxAction()
    {
        $em = $this->getDoctrine()->getManager();

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        if($user->hasRole('ROLE_SUCURSAL')){
            $query = $em->createQuery('SELECT d.id, d.codigo, d.nombre, d.apellido, d.localidad, d.imei, d.email, d.zona, s.descripcion 
          FROM DistribuidorBundle:Distribuidor d, PresisGeneralBundle:Sucursal s 
          WHERE d.sucursal=s
          AND d.sucursal=:sucursal');
          $query->setParameter('sucursal',$user->getSucursal());
        }else{
            $query = $em->createQuery('SELECT d.id, d.codigo, d.nombre, d.apellido, d.localidad, d.imei, d.email, d.zona, s.descripcion 
          FROM DistribuidorBundle:Distribuidor d, PresisGeneralBundle:Sucursal s 
          WHERE d.sucursal=s');
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return $json;
    }

    public function ajaxAction(){
        $json = $this->asajaxAction();

        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);
    }

    public function ajax4selectAction() {
        return new Response($this->ajax());
    }

    /**
     * Lists all Distribuidor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DistribuidorBundle:Distribuidor')->findAll();

        return $this->render('DistribuidorBundle:Distribuidor:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Distribuidor entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity = new Distribuidor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            if(!$entity->getCodigo()) {

                $entity->setCodigo($entity->getId());
                $em->persist($entity);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('distribuidor'));
        }

        return $this->render('DistribuidorBundle:Distribuidor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Distribuidor entity.
     *
     * @param Distribuidor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Distribuidor $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new DistribuidorType($securityContext), $entity, array(
            'action' => $this->generateUrl('distribuidor_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'AGREGAR DISTRIBUIDOR'));

        return $form;
    }

    /**
     * Displays a form to create a new Distribuidor entity.
     *
     */
    public function newAction()
    {
        $entity = new Distribuidor();
        $form   = $this->createCreateForm($entity);

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $sucursal = ($user->hasRole('ROLE_SUCURSAL'))? $user->getSucursal() : null;

        return $this->render('DistribuidorBundle:Distribuidor:new.html.twig', array(
            'entity' => $entity,
            'sucursal' => $sucursal,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Distribuidor entity.
     *
     */
    public function showAction($id)
    {
        $format = $this->get('request')->get('_format');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisDistribuidorBundle:Distribuidor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Distribuidor entity.');
        }

        return $this->render('PresisDistribuidorBundle:Distribuidor:edit.html.twig', array(
            'entity' => $entity,
            'attr'=>array('target'=>'_blank'),
        ));
    }

    /**
     * Displays a form to edit an existing Distribuidor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DistribuidorBundle:Distribuidor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Distribuidor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DistribuidorBundle:Distribuidor:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Distribuidor entity.
    *
    * @param Distribuidor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Distribuidor $entity)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();
        //PARA QUE ESTOS ROLES NO MODIFIQUEN DISTRIBUIDORES
        if($user->hasRole('ROLE_OPERATIVO')||($user->hasRole('ROLE_BACK_OFFICE'))||($user->hasRole('ROLE_ADMINISTRACION'))){
            $form = $this->createForm(new DistribuidorType($securityContext), $entity, array(
                'action' => $this->generateUrl('distribuidor_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'disabled' => true,
            ));
            //$form->add('submit', 'submit', array('label' => 'MODIFICAR DATOS'));
            return $form;
        }else{
            $form = $this->createForm(new DistribuidorType($securityContext), $entity, array(
                'action' => $this->generateUrl('distribuidor_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            ));
            $form->add('submit', 'submit', array('label' => 'MODIFICAR DATOS'));
            return $form;
        }
    }
    /**
     * Edits an existing Distribuidor entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DistribuidorBundle:Distribuidor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Distribuidor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('distribuidor'));
        }

        return $this->render('DistribuidorBundle:Distribuidor:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Distribuidor entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('DistribuidorBundle:Distribuidor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Distribuidor entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('distribuidor'));
    }

    /**
     * Creates a form to delete a Distribuidor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('distribuidor_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }


    public function presentismoAction()
    {
        return $this->render('DistribuidorBundle:Distribuidor:presentismo.html.twig');
    }

    public function savePresenteAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $post = $request->request->all();

        $fecha = \DateTime::createFromFormat('d/m/Y', $post['fecha']);

        $existe = $em->getRepository('DistribuidorBundle:Presentismo')->findOneBy(
            array(
                "codigo"=>$post['gestor_codigo'],
                "fecha"=>$fecha
            ));

        if($existe){
            throw $this->createNotFoundException('Existe');
        }

        $hora = date("H:i");

        $presentismo = new Presentismo();

        $presentismo->setApenom($post['gestor']);
        $presentismo->setCodigo($post['gestor_codigo']);
        $presentismo->setObservaciones($post['obs']);
        $presentismo->setEstado($post['presente']);
        $presentismo->setRecorrido($post['recorrido']);
        $presentismo->setHora(\DateTime::createFromFormat('H:i', $hora));
        $presentismo->setFecha($fecha);
        $presentismo->setTimestamp(new \DateTime());

        $em->persist($presentismo);
        $em->flush();

        return new Response("");
    }
}
