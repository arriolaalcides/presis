<?php

namespace Presis\ApiBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Security\Acl\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\Common\Collections\ArrayCollection;

class EtiController extends Controller
{
 /**
     * @RequestParam(name="username",nullable=false, description="Nombre de usuario")
     * @RequestParam(name="password",nullable=false, description="Contraseña asociada al usuario")
     * @RequestParam(name="ids",nullable=false,array=true,description="Array json de productos con su datos de tipo de carga, su peso, categoria o dimensiones")
     * @param ParamFetcher $paramFetcher
     * @return FOSView
     */

    public function postEtiAction(ParamFetcher $paramFetcher=null)
    {
        $usuario = $paramFetcher->get('username');
        $contra = $paramFetcher->get('password');
        $ids = $paramFetcher->get("ids");
    

        $view = View::create();
        $view->setStatusCode(200);
       
        $userManager = $this->get('fos_user.user_manager');

        $encoder_service = $this->get('security.encoder_factory');

           $user = $userManager->findUserByUsername($usuario);

           $encoder = $encoder_service->getEncoder($user);
        $encoded_pass = $encoder->encodePassword($contra, $user->getSalt());


        if ($user) {
            if (!($user->getPassword() == $encoded_pass)) {
                $view->setStatusCode(400);
                $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el usuario o contraseña"));
                return $view;
            }
        }else{
            $view->setStatusCode(400);
            $view->setData(Array("status"=>400,"message"=>"Ha ocurrido un error con el usuario o contraseña"));
            return $view;
        }

       
        $cont=0;
        $em = $this->getDoctrine()->getManager();
        $arrtotal=array();

        $arra=new ArrayCollection();
        $arrpa2=array();
        foreach($ids as $id) {

            $cont++;
            if ($cont==1){
                $arrpare=array();
            }

            $entity = $em->getRepository('PresisRetiroBundle:Retiro')->find($id);

            $arra->add($entity);
            $entity->setImpreso(true);
            $em->persist($entity);
            array_push($arrpare,$entity);
            array_push($arrpa2,$entity);

            if ($cont==2){
                array_push($arrtotal,$arrpare);

                $cont=0;
            }
        }
        if ($cont==1){
            array_push($arrtotal,$arrpare);
        }


        $em->flush();


        $format = $this->get('request')->get('_format');
        //  $format="pdf";
	$facade = $this->get('ps_pdf.facade');
        $response = new Response();
           $this->render(sprintf('PresisRetiroBundle:Default:eti.pdf.twig', $format), array(
            'vouchers' => $arrpa2,
            'attr'=>array('target'=>'_blank'),
        ),$response);
   $xml = $response->getContent();

                    $pdf = $facade->render($xml);
	     $view->setStatusCode(200);
            $view->setData(Array("status"=>200,"pdf"=>base64_encode($pdf)));

        return $view;


    }


}
