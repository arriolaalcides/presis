<?php

namespace Presis\ServicioBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{
    public function indexAction($name)
    {

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername("pamtru");
        $encoder_service = $this->get('security.encoder_factory');
        $encoder = $encoder_service->getEncoder($user);
        $encoded_pass = $encoder->encodePassword("1234", $user->getSalt());


        if ($user){
            if ($user->getPassword()==$encoded_pass){
                $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
                $this->get('security.context')->setToken($token);
                $this->get('session')->set('_security_main',serialize($token));
            }
        }else{
            $this->get('security.context')->setToken(null);
            $this->get('request')->getSession()->invalidate();
        }


        return $this->render('PresisServicioBundle:Default:index.html.twig');

    }

    protected function getServicioManager()
    {
        return $this->container->get('presis_servicio');
    }

}
