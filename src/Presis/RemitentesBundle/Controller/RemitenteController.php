<?php

namespace Presis\RemitentesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Presis\RemitentesBundle\Entity\Remitente;
use Presis\RemitentesBundle\Form\RemitenteType;
use Presis\RemitentesBundle\Form\RemitenteEditType;
use Presis\RemitentesBundle\Form\RemitenteNewType;
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
 * Remitente controller.
 *
 */
class RemitenteController extends Controller
{

    /*public function ajaxMemberAction(Request $request)
    {
        $value = $request->get('term');

        $em = $this->getDoctrine()->getEntityManager();
        $members = $em->getRepository('RemitentesBundle:Remitente')->findAjaxValue($value);

        $json = array();
        foreach ($members as $member) {
            $json[] = array(
                'label' => $member->getEmpresa(),
                'value' => $member->getId()
            );
        }

        $response = new Response();
        $response->setContent(json_encode($json));

        return $response;
    }*/


    public function listarRemitentesAction(Request  $request){

        $securityContext = $this->container->get('security.context');

        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        $attrs = $request->request->all();

        /*$query = $em->createQueryBuilder();
        $query = $em->createQuery(
            'SELECT d.id, d.codigo, d.empresa, d.remitente, d.celular, d.calle, d.altura, d.piso, 
             d.dpto, d.localidad, d.provincia, d.cp, d.otherInfo, c.empresa AS cliente, d.user FROM 
             RemitentesBundle:Remitente d, 
             PresisGeneralBundle:Cliente c
             WHERE
             d.cliente = c AND
             d.cliente = :cliente '
        );
        $query->setParameter('cliente', $user->getCliente());*/

        $query = $em->createQueryBuilder();
        $query->select('d,e')
            ->from('RemitentesBundle:Remitente','d')
            ->leftJoin('d.cliente','e')
            ->where('d.cliente = e')
            ->andWhere('d.cliente = :cliente')
            ->andWhere('d.empresa LIKE :param OR d.codigo LIKE :param OR d.remitente LIKE :param')
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
    public function eliminarAction(Request $request, $id_remitente)
    {

        $securityContext = $this->container->get('security.context');
        $user=$securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $retiro = $em->getRepository('RemitentesBundle:Remitente')->find($id_remitente);

        $em->remove($retiro);
        $em->flush();
        return new Response("ok");
    }

    /**
     * Lists all Remitente entities.
     *
     */
    public function indexAction()
    {
        
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('RemitentesBundle:Remitente')->findAll();

        return $this->render('RemitentesBundle:Remitente:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Remitente entity.
     *
     */
    public function createAction(Request $request)
    {
            $entity = new Remitente();
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

                return $this->redirect($this->generateUrl('remitente'));
            }

            return $this->render('RemitentesBundle:Remitente:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));
    }

    /**
     * Creates a form to create a Remitente entity.
     *
     * @param Remitente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Remitente $entity)
    {
        $form = $this->createForm(new RemitenteNewType(), $entity, array(
            'action' => $this->generateUrl('remitente_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Remitente entity.
     *
     */
    public function newAction()
    {
        $entity = new Remitente();
        $form   = $this->createCreateForm($entity);

        return $this->render('RemitentesBundle:Remitente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Remitente entity.
     *
     */
    public function getComboRemitenteAction($id)
    {

        //$entity = new Remitente();

        $em = $this->getDoctrine()->getManager();

        //$entity = $em->getRepository('RemitentesBundle:Remitente')->findByCliente($id);
        $entity = $em->getRepository('RemitentesBundle:Remitente')->findBy(array('cliente' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Remitente entity.');
        }

        $serializer = $this->container->get('serializer');
        $reports = $serializer->serialize($entity, 'json');
        return new Response($reports); // should be $reports as $doctrineobject is not serialized
        /*return new JsonResponse(array(
            'codigo' => $entity->getAltura()
        ));*/

    }

    /**
     * Finds and displays a Remitente entity.
     *
     */
    public function showAction($id)
    {
        $user=$this->get('security.context')->getToken()->getUser()->getCliente();

        $entity = new Remitente();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RemitentesBundle:Remitente')->findByCliente($user);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Remitente entity.');
        }

        return new JsonResponse(array(
            'codigo' => $entity->getCodigo(),
            'empresa' => $entity->getEmpresa(),    
            'remitente' => $entity->getRemitente(),
            'calle' => $entity->getCalle(),
            'altura' => $entity->getAltura(),
            'piso' => $entity->getPiso(),
            'dpto' => $entity->getDpto(),
            'localidad' => $entity->getLocalidad(),
            'provincia' => $entity->getProvincia(),
            'cp' => $entity->getCp()
            ));

    }
    public function ajaxHabilitadosAction(Request $request){
        $codcli=$request->get("codcli");
        $em=$this->getDoctrine()->getManager();
        $entity = $em->getRepository('RemitentesBundle:Remitente')->findByCliente($codcli);


        $serializer = $this->get('jms_serializer');
        $data=$serializer->serialize($entity, 'json');
        return new Response($data);
    }

    public function findAction($id)
    {

        $entity = new Remitente();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RemitentesBundle:Remitente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Remitente entity.');
        }

        return new JsonResponse(array(
            'codigo' => $entity->getCodigo(),
            'empresa' => $entity->getEmpresa(),
            'remitente' => $entity->getRemitente(),
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

    /**
     * Displays a form to edit an existing Remitente entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RemitentesBundle:Remitente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Remitente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RemitentesBundle:Remitente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Remitente entity.
    *
    * @param Remitente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Remitente $entity)
    {
        $form = $this->createForm(new RemitenteEditType(), $entity, array(
            'action' => $this->generateUrl('remitente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'MODIFICAR DATOS'));

        return $form;
    }
    /**
     * Edits an existing Remitente entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RemitentesBundle:Remitente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Remitente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('remitente_edit', array('id' => $id)));
        }

        return $this->render('RemitentesBundle:Remitente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Remitente entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RemitentesBundle:Remitente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Remitente entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('remitente'));
    }

    /**
     * Creates a form to delete a Remitente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('remitente_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
