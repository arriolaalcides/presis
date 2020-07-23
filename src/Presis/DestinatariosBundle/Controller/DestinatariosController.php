<?php

namespace Presis\DestinatariosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\DestinatariosBundle\Entity\Destinatarios;
use Presis\DestinatariosBundle\Form\DestinatariosType;
use Presis\DestinatariosBundle\Form\DestinatariosEditType;
use Presis\DestinatariosBundle\Form\DestinatariosNewType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Ps\PdfBundle\Annotation\Pdf;
use Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiro;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Presis\ConstanciaRetiroBundle\Form\ConstanciaRetiroType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Destinatarios controller.
 *
 */
class DestinatariosController extends Controller
{

    public function listarDestinatariosAction(Request  $request){

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $attrs = $request->request->all();

       /* $query = $em->createQueryBuilder();
        $query = $em->createQuery(
            'SELECT d.id, d.codigo, d.empresa, d.apellidoNombre, d.mail, d.celular, d.calle, d.altura, d.piso, 
             d.dpto, d.localidad, d.provincia, d.cp, d.otherInfo, c.empresa AS cliente, d.user FROM 
             DestinatariosBundle:Destinatarios d, 
             PresisGeneralBundle:Cliente c
             WHERE
             d.cliente = c AND
             d.cliente = :cliente '
        );
        $query->setParameter('cliente', $user->getCliente());*/
        $query = $em->createQueryBuilder();
        $query->select('d,e')
            ->from('DestinatariosBundle:Destinatarios','d')
            ->leftJoin('d.cliente','e')
            ->where('d.cliente = e')
            ->andWhere('d.cliente = :cliente')
            ->andWhere('d.empresa LIKE :param OR d.codigo LIKE :param OR d.apellidoNombre LIKE :param')
                ->setParameter('cliente', $user->getCliente())
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

    }

    /**
     * Deletes a Retiro entity.
     *
     */
    public function eliminarAction(Request $request, $id_destinatario)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $retiro = $em->getRepository('DestinatariosBundle:Destinatarios')->find($id_destinatario);

        $em->remove($retiro);
        $em->flush();
        return new Response("ok");
    }

    /**
     * Lists all Destinatarios entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DestinatariosBundle:Destinatarios')->findAll();

        return $this->render('DestinatariosBundle:Destinatarios:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Destinatarios entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Destinatarios();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $securityContext = $this->container->get('security.context');
            $user=$securityContext->getToken()->getUser();

            $entity->setCliente($user->getCliente());
            $entity->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('destinatarios_new'));
        }

        return $this->render('DestinatariosBundle:Destinatarios:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Destinatarios entity.
     *
     * @param Destinatarios $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Destinatarios $entity)
    {
        $form = $this->createForm(new DestinatariosNewType(), $entity, array(
            'action' => $this->generateUrl('destinatarios_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Destinatarios entity.
     *
     */
    public function newAction()
    {
        $entity = new Destinatarios();
        $form   = $this->createCreateForm($entity);

        return $this->render('DestinatariosBundle:Destinatarios:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Remitente entity.
     *
     */
    public function getComboDestinatarioAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DestinatariosBundle:Destinatarios')->findBy(array('cliente' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destinatarios entity.');
        }

        $serializer = $this->container->get('serializer');
        $reports = $serializer->serialize($entity, 'json');
        return new Response($reports);
    }

    /**
     * Finds and displays a Destinatarios entity.
     *
     */
    public function showAction($id)
    {
        
        $entity = new Destinatarios();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DestinatariosBundle:Destinatarios')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destinatario entity.');
        }

        return new JsonResponse(array(
            'codigo' => $entity->getCodigo(),
            'empresa' => $entity->getEmpresa(),    
            'destinatario' => $entity->getDestinatario(),
            'calle' => $entity->getCalle(),
            'altura' => $entity->getAltura(),
            'piso' => $entity->getPiso(),
            'depto' => $entity->getDpto(),
            'localidad' => $entity->getLocalidad(),
            'provincia' => $entity->getProvincia(),
            'cp' => $entity->getCp(),
            'celular' => $entity->getCelular(),
            'apellidoNombre' => $entity->getApellidoNombre(),
            'mail' => $entity->getMail(),
            'otherInfo' => $entity->getOtherInfo(),
            'kms' => $entity->getKms(),
            'cuit' => $entity->getCuit()
            ));
        /*$em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DestinatariosBundle:Destinatarios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destinatarios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DestinatariosBundle:Destinatarios:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));*/
    }

    /**
     * Displays a form to edit an existing Destinatarios entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DestinatariosBundle:Destinatarios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destinatarios entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DestinatariosBundle:Destinatarios:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function findAction($id)
    {

        $entity = new Destinatarios();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DestinatariosBundle:Destinatarios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destinatario entity.');
        }

        return new JsonResponse(array(
            'codigo' => $entity->getCodigo(),
            'empresa' => $entity->getEmpresa(),
            'destinatario' => $entity->getDestinatario(),
            'calle' => $entity->getCalle(),
            'altura' => $entity->getAltura(),
            'piso' => $entity->getPiso(),
            'dpto' => $entity->getDpto(),
            'localidad' => $entity->getLocalidad(),
            'provincia' => $entity->getProvincia(),
            'email' => $entity->getMail(),
            'celular' => $entity->getCelular(),
            'cp' => $entity->getCp(),
            'otherInfo' => $entity->getOtherInfo()
        ));

    }

    /**
    * Creates a form to edit a Destinatarios entity.
    *
    * @param Destinatarios $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Destinatarios $entity)
    {
        $form = $this->createForm(new DestinatariosEditType(), $entity, array(
            'action' => $this->generateUrl('destinatarios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'ACTUALIZAR DATOS'));

        return $form;
    }
    /**
     * Edits an existing Destinatarios entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DestinatariosBundle:Destinatarios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destinatarios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('destinatarios_edit', array('id' => $id)));
        }

        return $this->render('DestinatariosBundle:Destinatarios:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Destinatarios entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DestinatariosBundle:Destinatarios')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Destinatarios entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('destinatarios'));
    }

    /**
     * Creates a form to delete a Destinatarios entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('destinatarios_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
