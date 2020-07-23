<?php

namespace Presis\ApiBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\View\View;

use Presis\GestionCelBundle\Entity\GestionCel;
use Presis\EstadoBundle\Entity\Estado;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\RetiroBundle\Entity\Sender;
use Presis\TrackerBundle\DependencyInjection\TrackerExtension;
use Presis\TrackerBundle\Entity\Tracker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Security\Acl\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class ConsultarguiasController extends Controller
{
    /**
     * @RequestParam(name="username",nullable=false, description="Nombre de usuario")
     * @RequestParam(name="password",nullable=false, description="Contraseña asociada al usuario")
     * @RequestParam(name="id_guia",nullable=false, description="Numero de guia incorrecto")
     * @RequestParam(name="nro_imei",nullable=false, description="Numero de imei incorrecto")
     * @param ParamFetcher $paramFetcher
     * @ApiDoc(
     *     statusCodes={
     *         200="Devuelto cuando la solicitud fue procesada correctamente.",
     *         400="Devuelto cuando hay algun problema con los parametros.",
     *         500="Devuelto cuando no se encuentra al usuario."
     *     },
     *  description="Consulta estado de las guias",
     *
     *  parameters={
     *         {"name"="password","dataType"="string",}
     * }
     * )
     * @return FOSView
     */
    
    /*
 * http://localhost:8000/api/v1/consultarguias.json
 * {
      "username": "backoffice",
      "password": "123456",
      "id_guia": "189",
      "nro_imei": "una sucursal de prueba",
    }

 */

    public function postConsultarguiaAction(ParamFetcher $paramFetcher=null)
    {
        $username = $paramFetcher->get('username');
        $contra = $paramFetcher->get('password');
        $idGuia = $paramFetcher->get("id_guia");
        $nroImei = $paramFetcher->get("nro_imei");
        $view=View::create();
        $view->setStatusCode(200);        
         
        $manager=$this->getDoctrine()->getManager();
        
        $userManager = $this->get('fos_user.user_manager');
        
        $encoder_service = $this->get('security.encoder_factory');
            
        $user = $userManager->findUserByUsername($username);
                       
        if ($user) {
            $encoder = $encoder_service->getEncoder($user);
               
            $encoded_pass = $encoder->encodePassword($contra, $user->getSalt());
            
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
        
        if (isset($idGuia)){
            if ((!is_numeric($idGuia)) || ($idGuia ==='') || (null===$idGuia)){
                $view->setStatusCode(400);
                $view->setData(Array("status"=>400,"message"=>"Id_Guia debe tener valor numerico"));
                return $view;
            }
        }
        
        if (isset($nroImei)){
            if ((strlen($nroImei)!=15) || ($nroImei ==='') || (null===$nroImei)){
                $view->setStatusCode(400);
                $view->setData(Array("status"=>400,"message"=>"El numero de IMEI es erroneo"));
                return $view;
            }
        }
         
         $query = $manager->createQuery(
            'SELECT r.id, g.sucursal,g.nroserie,e.nombre 
             FROM PresisRetiroBundle:Retiro r,
             EstadoBundle:Estado e,
             GestionCelBundle:GestionCel g
             WHERE
             g.nroserie=:nroImei AND 
             r.id=:idGuia AND
             r.gestioncel = g AND
             g.estado = e '
            );
        $query->setParameter('idGuia',$idGuia);
        $query->setParameter('nroImei',$nroImei);
        
        $retiros = $query->getResult();
         
        if (count($retiros) == 0){
                $view->setStatusCode(400);
                $view->setData(Array("status"=>400,"message"=>"No hay datos con el IMEI y guia"));
                return $view;
        }
        
          $view->setData(Array("id"=>$retiros[0]['id'],
                                             "sucursal"=>$retiros[0]['sucursal'],
                                             "Imei"=>$retiros[0]['nroserie'],
                                             "estado"=>$retiros[0]['nombre'])
                        );
                
        return $view;
        
    }   
}
