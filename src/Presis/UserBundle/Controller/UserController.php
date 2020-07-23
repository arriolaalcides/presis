<?php

namespace Presis\UserBundle\Controller;

use Presis\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\UserBundle\Entity\User;
use Presis\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Response;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    public function ajaxAction(){

        $em=$this->getDoctrine()->getManager();
        $user=$this->get('security.context')->getToken()->getUser();
        if(!$user->hasRole('ROLE_ADMIN') AND !$user->hasRole('ROLE_CLIENTE') AND !$user->hasRole('ROLE_VENDEDOR')){
            $query = $em->createQuery(
                'SELECT u.id,u.username,u.email,u.roles,c.empresa,v.nombre,u.userAdmin,s.descripcion
                FROM PresisUserBundle:User u
                LEFT JOIN u.cliente c
                LEFT JOIN u.vendedor v
                LEFT JOIN u.sucursal s
                where u.username=:username');
            $query->setParameter('username',$user->getUsername());
        }else{
            if($user->hasRole('ROLE_ADMIN')){
                $query = $em->createQuery(
                    'SELECT u.id,u.username,u.email,u.roles,c.empresa,v.nombre,u.userAdmin,s.descripcion
                FROM PresisUserBundle:User u
                LEFT JOIN u.cliente c
                LEFT JOIN u.vendedor v
                LEFT JOIN u.sucursal s');
            }
            if ($user->hasRole('ROLE_CLIENTE')) {
                if($user->isUserAdmin()) {
                    $query = $em->createQuery(
                        'SELECT u.id,u.username,u.email,u.roles,c.empresa,v.nombre,u.userAdmin,s.descripcion
                FROM PresisUserBundle:User u
                LEFT JOIN u.cliente c
                LEFT JOIN u.vendedor v
                LEFT JOIN u.sucursal s
                where u.cliente=:cliente');
                    $query->setParameter('cliente',$user->getCliente());
                }else{
                    $query = $em->createQuery(
                        'SELECT u.id,u.username,u.email,u.roles,c.empresa,v.nombre,u.userAdmin,s.descripcion
                FROM PresisUserBundle:User u
                LEFT JOIN u.cliente c
                LEFT JOIN u.vendedor v
                LEFT JOIN u.sucursal s
                where u.username=:username');
                    $query->setParameter('username',$user->getUsername());
                }
            }
            if($user->hasRole('ROLE_VENDEDOR')){
                $query = $em->createQuery(
                    'SELECT u.id,u.username,u.email,u.roles,c.empresa,v.nombre,s.descripcion,u.userAdmin
                FROM PresisUserBundle:User u
                LEFT JOIN u.cliente c
                LEFT JOIN u.vendedor v
                LEFT JOIN u.sucursal s
                WHERE u.vendedor=:vendedor OR 
                u.cliente IN (SELECT cli FROM PresisGeneralBundle:Cliente cli WHERE cli.vendedor=:vendedor)');
                $query->setParameter('vendedor', $user->getVendedor());
            }
        }

        $users = $query->getResult();

        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($users, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);

    }
    /**
     * Lists all User entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisUserBundle:User')->findAll();

        return $this->render('PresisUserBundle:User:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setEnabled(true);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return $this->render('PresisUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $securityContext = $this->container->get('security.context');
        $form = $this->createForm(new RegistrationFormType($securityContext), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Agregar Usuario','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return $this->render('PresisUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisUserBundle:User:show.html.twig', array(
            'entity'      => $entity
           // 'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisUserBundle:User')->find($id);
        $userManager = $this->get('fos_user.user_manager');
        $user=$userManager->findUserByUsername($entity->getUsername());
        $entity->setPlainPassword($user->getPlainPassword());
        $entity->setPassword($user->getPassword());


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PresisUserBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
            $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new RegistrationFormType($securityContext), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Grabar','attr' => array('class'=> 'btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PresisUserBundle:User')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $userManager = $this->get('fos_user.user_manager');

            $em->flush();
            $userManager->updateUser($entity,true);




            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }

        return $this->render('PresisUserBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PresisUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class'=> 'btn btn-danger')))
            ->getForm()
        ;
    }

}
