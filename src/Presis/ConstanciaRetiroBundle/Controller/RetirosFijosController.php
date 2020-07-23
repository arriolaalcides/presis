<?php

namespace Presis\ConstanciaRetiroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Presis\ConstanciaRetiroBundle\Entity\RetirosFijos;
use Presis\ConstanciaRetiroBundle\Form\RetirosFijosType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
/**
 * RetirosFijos controller.
 *
 */
class RetirosFijosController extends Controller
{

    public function deshabilitarAction(Request $request, $idFijo)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $fijo = $em->getRepository('ConstanciaRetiroBundle:RetirosFijos')->find($idFijo);

        $fijo->setIsHabilitado(false);

        $em->flush();
        return new Response("ok");
    }

    public function habilitarAction(Request $request, $idFijo)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $fijo = $em->getRepository('ConstanciaRetiroBundle:RetirosFijos')->find($idFijo);

        $fijo->setIsHabilitado(true);

        $em->flush();
        return new Response("ok");
    }

    public function listadoAjaxAction(Request $request)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $attrs = $request->request->all();

        $query = $em->createQueryBuilder();
        $query->select('d,e')
            ->from('ConstanciaRetiroBundle:RetirosFijos','d')
            ->leftJoin('d.cliente','e')
            ->where('d.cliente = e')
            ->andWhere('e.empresa LIKE :param OR d.dias LIKE :param')
            ->setParameter('param', '%'.$attrs['search'].'%');

        $dontPaginate = true;
        if ($attrs["limite"] > 0) {
            $query->setFirstResult($attrs["pagina"] * $attrs["limite"])
                ->setMaxResults($attrs["limite"]);
            $dontPaginate = false;
        }
        unset($attrs["pagina"]);
        unset($attrs["limite"]);

        $paginator = new Paginator($query, $fetchJoinCollection = false);

        $result["total"] = count($paginator);
        $result["rows"] = $paginator->getQuery()->getResult();

        //$result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);


        /*$query = $em->getRepository("ConstanciaRetiroBundle:RetirosFijos")->createQueryBuilder('c');

        $query->orderBy('c.id', 'DESC');

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $result = $paginator->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($result, "json");

        return new Response($json);*/
    }


    /**
     * Lists all RetirosFijos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ConstanciaRetiroBundle:RetirosFijos')->findAll();

        return $this->render('ConstanciaRetiroBundle:RetirosFijos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new RetirosFijos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new RetirosFijos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('retirosfijos_new', array('id' => $entity->getId())));
        }

        return $this->render('ConstanciaRetiroBundle:RetirosFijos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a RetirosFijos entity.
     *
     * @param RetirosFijos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RetirosFijos $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new RetirosFijosType($securityContext), $entity, array(
            'action' => $this->generateUrl('retirosfijos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'GUARDAR RETIRO'));

        return $form;
    }

    /**
     * Displays a form to create a new RetirosFijos entity.
     *
     */
    public function newAction()
    {
        $entity = new RetirosFijos();
        $form   = $this->createCreateForm($entity);

        return $this->render('ConstanciaRetiroBundle:RetirosFijos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a RetirosFijos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConstanciaRetiroBundle:RetirosFijos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RetirosFijos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConstanciaRetiroBundle:RetirosFijos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing RetirosFijos entity.
     *
     */
    public function editAction($id)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConstanciaRetiroBundle:RetirosFijos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RetirosFijos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConstanciaRetiroBundle:RetirosFijos:edit.html.twig', array(
            'entity' => $entity,
            'sucursal' => $user->getSucursal(),
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a RetirosFijos entity.
    *
    * @param RetirosFijos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(RetirosFijos $entity)
    {
        $securityContext = $this->container->get('security.context');

        $form = $this->createForm(new RetirosFijosType($securityContext), $entity, array(
            'action' => $this->generateUrl('retirosfijos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing RetirosFijos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConstanciaRetiroBundle:RetirosFijos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RetirosFijos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('retirosfijos_edit', array('id' => $id)));
        }

        return $this->render('ConstanciaRetiroBundle:RetirosFijos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a RetirosFijos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ConstanciaRetiroBundle:RetirosFijos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RetirosFijos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('retirosfijos'));
    }

    /**
     * Creates a form to delete a RetirosFijos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('retirosfijos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Deletes a Retiro entity.
     *
     */
    public function eliminarAction(Request $request, $idFijo)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $fijo = $em->getRepository('ConstanciaRetiroBundle:RetirosFijos')->find($idFijo);

        $em->remove($fijo);
        $em->flush();
        return new Response("ok");
    }

}
